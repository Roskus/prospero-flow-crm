/*
 * CRM client. The only place that knows the API token: the popup and the panel ask for
 * everything through runtime messages and never see it. The content script never calls it
 * with the token either. The password is sent once to login and never stored.
 *
 * Session kept in chrome.storage.local (never storage.sync, it would copy the token to
 * other devices): { crmUrl, token, expiresAt, user, company }.
 */

const SESSION_KEY = 'session';
const COUNTRIES_KEY = 'countries';
const COUNTRY_LANGUAGES = ['en', 'es'];
const REFRESH_MARGIN_MS = 5 * 60 * 1000;

class CrmError extends Error {
  constructor(code, details) {
    super(code);
    this.code = code;
    this.details = details || {};
  }
}

async function loadSession() {
  const stored = await chrome.storage.local.get(SESSION_KEY);
  return stored[SESSION_KEY] || null;
}

async function saveSession(session) {
  await chrome.storage.local.set({ [SESSION_KEY]: session });
}

async function clearSession() {
  await chrome.storage.local.remove([SESSION_KEY, COUNTRIES_KEY]);
}

function crmOrigin(url) {
  const parsed = new URL(url.includes('://') ? url : 'https://' + url);
  if (parsed.protocol !== 'https:' && parsed.protocol !== 'http:') {
    throw new CrmError('invalid_url');
  }
  return parsed.origin;
}

function tokenFields(body) {
  return {
    token: body.access_token,
    expiresAt: Date.now() + Number(body.expires_in || 0) * 1000,
  };
}

/** Plain HTTP call to the CRM; turns network and HTTP failures into CrmError codes. */
async function request(crmUrl, method, path, { token, body, query } = {}) {
  const url = new URL(crmUrl + '/api' + path);
  Object.entries(query || {}).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') {
      url.searchParams.set(key, value);
    }
  });

  const headers = { Accept: 'application/json' };
  if (body !== undefined) {
    headers['Content-Type'] = 'application/json';
  }
  if (token) {
    headers.Authorization = 'Bearer ' + token;
  }

  let response;
  try {
    response = await fetch(url, {
      method,
      headers,
      body: body === undefined ? undefined : JSON.stringify(body),
      credentials: 'omit',
    });
  } catch (error) {
    throw new CrmError('unreachable');
  }

  let data = null;
  try {
    data = await response.json();
  } catch (error) {
    if (response.ok || response.status === 422) {
      throw new CrmError('unreachable');
    }
  }

  if (response.ok) {
    return data;
  }
  if (response.status === 401) {
    throw new CrmError('unauthorized');
  }
  if (response.status === 403 && data && data.error === 'Inactive user') {
    throw new CrmError('inactive');
  }
  if (response.status === 403) {
    throw new CrmError('forbidden');
  }
  if (response.status === 422) {
    throw new CrmError('validation', { message: data && data.message, errors: (data && data.errors) || {} });
  }
  if (response.status === 429) {
    throw new CrmError('too_many_attempts');
  }
  throw new CrmError('http', { status: response.status });
}

async function refresh(session) {
  try {
    const body = await request(session.crmUrl, 'POST', '/auth/refresh', { token: session.token });
    const renewed = { ...session, ...tokenFields(body) };
    await saveSession(renewed);
    return renewed;
  } catch (error) {
    if (error.code === 'unreachable') {
      throw error;
    }
    await clearSession();
    throw new CrmError('session_expired');
  }
}

/**
 * Authenticated call: refreshes the token when it expires within 5 minutes, retries once
 * after a refresh on 401, and drops the session when it cannot be refreshed or the user
 * is inactive.
 */
async function api(method, path, options = {}) {
  let session = await loadSession();
  if (!session) {
    throw new CrmError('logged_out');
  }
  if (session.expiresAt - Date.now() < REFRESH_MARGIN_MS) {
    session = await refresh(session);
  }

  try {
    return await request(session.crmUrl, method, path, { ...options, token: session.token });
  } catch (error) {
    if (error.code === 'inactive') {
      await clearSession();
      throw error;
    }
    if (error.code !== 'unauthorized') {
      throw error;
    }
  }

  session = await refresh(session);
  try {
    return await request(session.crmUrl, method, path, { ...options, token: session.token });
  } catch (error) {
    if (error.code === 'unauthorized' || error.code === 'inactive') {
      await clearSession();
      throw new CrmError(error.code === 'inactive' ? 'inactive' : 'session_expired');
    }
    throw error;
  }
}

function publicSession(session) {
  if (!session) {
    return null;
  }
  return { crmUrl: session.crmUrl, user: session.user, company: session.company };
}

async function login({ crmUrl, email, password }) {
  const origin = crmOrigin(crmUrl);
  let body;
  try {
    body = await request(origin, 'POST', '/auth/login', { body: { email, password } });
  } catch (error) {
    throw new CrmError(error.code === 'unauthorized' ? 'invalid_credentials' : error.code, error.details);
  }
  if (!body || !body.access_token) {
    throw new CrmError('unreachable');
  }

  const token = tokenFields(body);
  const me = await request(origin, 'POST', '/auth/me', { token: token.token });
  let company = null;
  try {
    company = await request(origin, 'GET', '/company/' + me.company_id, { token: token.token });
  } catch (error) {
    company = null;
  }

  const session = {
    crmUrl: origin,
    ...token,
    user: { id: me.id, firstName: me.first_name, lastName: me.last_name, email: me.email },
    company: company ? { id: company.id, name: company.name, countryId: company.country_id } : null,
  };
  await saveSession(session);
  return publicSession(session);
}

async function logout() {
  try {
    await api('POST', '/auth/logout');
  } catch (error) {
    // The session is removed locally even when the CRM cannot be reached.
  }
  await clearSession();
  return null;
}

/** Leads and customers of the company whose name or business name contains the text. */
async function searchOwners({ text }) {
  const query = { search: text, per_page: 10 };
  const [leads, customers] = await Promise.all([
    api('GET', '/lead', { query }),
    api('GET', '/customer', { query }),
  ]);
  const owner = (type) => (record) => ({
    type,
    id: record.id,
    name: record.name,
    businessName: record.business_name || '',
  });
  return [
    ...(leads.leads || []).map(owner('lead')),
    ...(customers.customers || []).map(owner('customer')),
  ];
}

async function ownerName(type, id) {
  try {
    const body = await api('GET', '/' + type + '/' + id);
    return (body[type] && body[type].name) || '';
  } catch (error) {
    return '';
  }
}

/** Contacts of the company already saved with this LinkedIn profile, with their owner. */
async function findContactByLinkedin({ url }) {
  const body = await api('GET', '/contact', { query: { linkedin: url } });
  const session = await loadSession();
  return Promise.all((body.contacts || []).map(async (contact) => {
    const type = contact.customer_id ? 'customer' : 'lead';
    const id = contact.customer_id || contact.lead_id;
    return {
      id: contact.id,
      name: [contact.first_name, contact.last_name].filter(Boolean).join(' '),
      owner: { type, id, name: await ownerName(type, id), url: ownerUrl(session, type, id) },
    };
  }));
}

function ownerUrl(session, type, id) {
  return session.crmUrl + '/' + type + '/show/' + id;
}

/** New lead named after the company, assigned to the user, in the form or company country. */
async function createLead({ name, countryId, linkedin }) {
  const session = await loadSession();
  if (!session) {
    throw new CrmError('logged_out');
  }
  const body = {
    name,
    seller_id: session.user.id,
    country_id: countryId || (session.company && session.company.countryId) || undefined,
  };
  if (linkedin) {
    body.linkedin = linkedin;
  }
  const lead = await api('POST', '/lead', { body });
  return { type: 'lead', id: lead.id, name: lead.name, url: ownerUrl(session, 'lead', lead.id) };
}

/** Saves the contact; never sends a photo, the form has no photo field. */
async function createContact({ contact }) {
  const body = await api('POST', '/contact', { body: contact });
  const session = await loadSession();
  const type = contact.customer_id ? 'customer' : 'lead';
  const id = contact.customer_id || contact.lead_id;
  return { id: body.contact.id, owner: { type, id, url: ownerUrl(session, type, id) } };
}

/**
 * Countries of the CRM (its countries library) in English and Spanish, read once per session:
 * names in both languages find the country of a LinkedIn location, and fill the form select.
 */
async function countries() {
  const stored = await chrome.storage.local.get(COUNTRIES_KEY);
  if (stored[COUNTRIES_KEY]) {
    return stored[COUNTRIES_KEY];
  }
  const lists = {};
  await Promise.all(COUNTRY_LANGUAGES.map(async (lang) => {
    lists[lang] = (await api('GET', '/country', { query: { lang } })).countries || [];
  }));
  await chrome.storage.local.set({ [COUNTRIES_KEY]: lists });
  return lists;
}

const handlers = {
  login,
  logout,
  session: async () => publicSession(await loadSession()),
  countries,
  searchOwners,
  findContactByLinkedin,
  createLead,
  createContact,
};

/** The LinkedIn content script may only ask whether there is a session; CRM calls come from extension pages. */
function isAllowed(message, sender) {
  if (sender.id !== chrome.runtime.id) {
    return false;
  }
  const fromExtensionPage = typeof sender.url === 'string' && sender.url.startsWith(chrome.runtime.getURL(''));
  return fromExtensionPage || message.type === 'session';
}

chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
  const handler = message && handlers[message.type];
  if (!handler || !isAllowed(message, sender)) {
    return false;
  }
  handler(message.payload || {})
    .then((data) => sendResponse({ ok: true, data }))
    .catch((error) => sendResponse({
      ok: false,
      error: { code: error.code || 'unexpected', ...(error.details || {}) },
    }));
  return true;
});
