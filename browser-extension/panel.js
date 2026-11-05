/*
 * Contact form shown on the LinkedIn profile. Every field is editable before saving and the
 * CRM is only called through background.js. No photo field and no image anywhere.
 */

const form = document.getElementById('contact-form');
const ownerSearch = document.getElementById('owner-search');
const ownerResults = document.getElementById('owner-results');
const ownerSelected = document.getElementById('owner-selected');
const LINKEDIN_ORIGIN = 'https://www.linkedin.com';
const SEARCH_DELAY_MS = 300;

/** { type: 'lead' | 'customer', id, name } or { type: 'newLead', name }. */
let owner = null;
let profile = null;
let searchTimer = null;
let searchRun = 0;

function field(name) {
  return form.elements.namedItem(name);
}

function show(id, visible) {
  document.getElementById(id).hidden = !visible;
}

function fatal(error) {
  const element = document.getElementById('fatal');
  element.textContent = errorMessage(error);
  element.hidden = false;
  show('loading', false);
  form.hidden = true;
}

function clearErrors() {
  form.querySelectorAll('.invalid').forEach((element) => element.classList.remove('invalid'));
  form.querySelectorAll('label .field-error').forEach((element) => element.remove());
  form.querySelectorAll('[data-error-for]').forEach((element) => {
    element.hidden = true;
  });
  show('form-error', false);
}

/** Shows an error under its field without touching what the user typed. */
function fieldError(name, message) {
  if (name === 'owner') {
    const element = form.querySelector('[data-error-for="owner"]');
    element.textContent = message;
    element.hidden = false;
    return;
  }
  const input = field(name);
  if (!input) {
    return;
  }
  input.classList.add('invalid');
  const error = document.getElementById('field-error-template').content.firstElementChild.cloneNode(true);
  error.textContent = message;
  input.closest('label').appendChild(error);
}

function formError(message) {
  const element = document.getElementById('form-error');
  element.textContent = message;
  element.hidden = false;
}

function ownerLabel(candidate) {
  if (candidate.type === 'newLead') {
    return t('createLead', [candidate.name]);
  }
  return t(candidate.type === 'lead' ? 'ownerLead' : 'ownerCustomer', [candidate.name]);
}

function selectOwner(candidate) {
  owner = candidate;
  ownerSelected.textContent = candidate ? t('ownerSelected', [ownerLabel(candidate)]) : '';
  ownerSelected.hidden = !candidate;
  ownerResults.querySelectorAll('button').forEach((button) => {
    button.setAttribute('aria-pressed', String(button.ownerCandidate === candidate));
  });
}

function renderOwners(candidates) {
  ownerResults.replaceChildren(...candidates.map((candidate) => {
    const item = document.createElement('li');
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'result';
    button.textContent = ownerLabel(candidate);
    button.ownerCandidate = candidate;
    button.addEventListener('click', () => selectOwner(candidate));
    item.appendChild(button);
    return item;
  }));
}

/** Leads and customers of the company matching the text, plus creating a lead with that name. */
async function searchOwners(text, preselect) {
  const run = ++searchRun;
  const name = text.trim();
  let candidates = [];
  if (name) {
    try {
      candidates = await callBackground('searchOwners', { text: name });
    } catch (error) {
      if (run === searchRun) {
        fieldError('owner', errorMessage(error));
      }
      return;
    }
  }
  if (run !== searchRun) {
    return;
  }
  const newLead = name ? [{ type: 'newLead', name }] : [];
  renderOwners([...candidates, ...newLead]);

  if (preselect) {
    const sameName = candidates.find((candidate) => normalizeText(candidate.name) === normalizeText(name));
    selectOwner(sameName || null);
  }
}

ownerSearch.addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => searchOwners(ownerSearch.value, false), SEARCH_DELAY_MS);
});

function fillCountries(lists) {
  const language = chrome.i18n.getUILanguage().toLowerCase().startsWith('es') ? 'es' : 'en';
  const select = field('country_id');
  (lists[language] || lists.en || []).forEach((country) => {
    select.add(new Option(country.name, country.id));
  });
}

async function checkDuplicate(url) {
  if (!url) {
    return;
  }
  try {
    const contacts = await callBackground('findContactByLinkedin', { url });
    if (contacts.length === 0) {
      return;
    }
    const existing = contacts[0];
    document.getElementById('duplicate-text').textContent =
      t('duplicateWarning', [existing.owner.name || ownerLabel(existing.owner)]);
    document.getElementById('duplicate-link').href = existing.owner.url;
    show('duplicate', true);
  } catch (error) {
    // The warning is a help; the form still works without it.
  }
}

async function start(receivedProfile) {
  profile = receivedProfile;
  let countryLists;
  try {
    countryLists = await callBackground('countries');
  } catch (error) {
    fatal(error);
    return;
  }

  fillCountries(countryLists);
  const location = splitLocation(profile.location, countryIndex(countryLists));
  const values = {
    contact_first_name: profile.firstName,
    contact_last_name: profile.lastName,
    contact_job_title: profile.jobTitle,
    company: profile.company,
    contact_linkedin: profile.linkedin,
    country_id: location.countryId,
    province: location.province,
    city: location.city,
  };
  Object.entries(values).forEach(([name, value]) => {
    field(name).value = value || '';
  });

  show('loading', false);
  form.hidden = false;
  field('contact_first_name').focus();

  ownerSearch.value = profile.company || '';
  await Promise.all([searchOwners(ownerSearch.value, true), checkDuplicate(profile.linkedin)]);
}

function contactPayload(ownerId) {
  const payload = {
    [owner.type === 'customer' ? 'customer_id' : 'lead_id']: ownerId,
  };
  ['contact_first_name', 'contact_last_name', 'contact_job_title', 'contact_email', 'contact_phone',
    'contact_linkedin', 'country_id', 'province', 'city'].forEach((name) => {
    const value = field(name).value.trim();
    if (value) {
      payload[name] = value;
    }
  });
  return payload;
}

function showValidationErrors(error, fieldMap) {
  const errors = (error && error.errors) || {};
  let shown = false;
  Object.entries(errors).forEach(([name, messages]) => {
    const target = fieldMap[name] || name;
    if (target === 'owner' || field(target)) {
      fieldError(target, [].concat(messages)[0]);
      shown = true;
    }
  });
  formError(shown ? errorMessage(error) : (error && error.message) || errorMessage(error));
}

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  clearErrors();

  let valid = true;
  if (!field('contact_first_name').value.trim()) {
    fieldError('contact_first_name', t('firstNameRequired'));
    valid = false;
  }
  if (!owner) {
    fieldError('owner', t('ownerRequired'));
    valid = false;
  }
  if (!valid) {
    return;
  }

  const save = document.getElementById('save');
  save.disabled = true;
  try {
    if (owner.type === 'newLead') {
      try {
        const lead = await callBackground('createLead', {
          name: owner.name,
          countryId: field('country_id').value,
          linkedin: normalizeText(owner.name) === normalizeText(profile.company) ? profile.companyLinkedin : '',
        });
        selectOwner({ type: 'lead', id: lead.id, name: lead.name });
      } catch (error) {
        if (error.code === 'validation') {
          showValidationErrors(error, { name: 'owner', seller_id: 'owner', country_id: 'country_id' });
        } else {
          formError(errorMessage(error));
        }
        return;
      }
    }

    try {
      const saved = await callBackground('createContact', { contact: contactPayload(owner.id) });
      document.getElementById('saved-link').href = saved.owner.url;
      form.hidden = true;
      show('saved', true);
    } catch (error) {
      if (error.code === 'validation') {
        showValidationErrors(error, { lead_id: 'owner', customer_id: 'owner' });
      } else {
        formError(errorMessage(error));
      }
    }
  } finally {
    save.disabled = false;
  }
});

document.getElementById('close').addEventListener('click', () => {
  window.parent.postMessage({ type: 'close' }, LINKEDIN_ORIGIN);
});

window.addEventListener('message', (event) => {
  if (event.source !== window.parent || event.origin !== LINKEDIN_ORIGIN || profile) {
    return;
  }
  if (event.data && event.data.type === 'profile' && event.data.profile) {
    start(event.data.profile);
  }
});
