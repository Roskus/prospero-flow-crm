/*
 * Login form, current user and logout. The CRM origin permission is requested here, from
 * the user's click, because browsers only grant optional permissions on a user gesture.
 */

const loginForm = document.getElementById('login-form');
const account = document.getElementById('account');
const crmUrlInput = document.getElementById('crm-url');
const loginError = document.getElementById('login-error');
const httpWarning = document.getElementById('http-warning');

function showError(message) {
  loginError.textContent = message;
  loginError.hidden = !message;
}

function originOf(value) {
  try {
    const url = new URL(value.includes('://') ? value : 'https://' + value);
    return ['https:', 'http:'].includes(url.protocol) ? url.origin : null;
  } catch (error) {
    return null;
  }
}

function render(session) {
  loginForm.hidden = Boolean(session);
  account.hidden = !session;
  if (!session) {
    return;
  }
  document.getElementById('user-name').textContent =
    [session.user.firstName, session.user.lastName].filter(Boolean).join(' ') || session.user.email;
  document.getElementById('company-name').textContent = session.company ? session.company.name : '';
  const link = document.getElementById('crm-link');
  link.href = session.crmUrl;
  link.textContent = session.crmUrl;
}

crmUrlInput.addEventListener('input', () => {
  httpWarning.hidden = !crmUrlInput.value.trim().toLowerCase().startsWith('http://');
});

loginForm.addEventListener('submit', async (event) => {
  event.preventDefault();
  showError('');

  const origin = originOf(crmUrlInput.value.trim());
  if (!origin) {
    showError(errorMessage({ code: 'invalid_url' }));
    return;
  }

  const granted = await chrome.permissions.request({ origins: [origin + '/*'] });
  if (!granted) {
    showError(errorMessage({ code: 'permission_denied' }));
    return;
  }

  const button = loginForm.querySelector('button');
  button.disabled = true;
  const password = document.getElementById('password');
  try {
    const session = await callBackground('login', {
      crmUrl: origin,
      email: document.getElementById('email').value.trim(),
      password: password.value,
    });
    await chrome.storage.local.set({ lastCrmUrl: origin });
    render(session);
  } catch (error) {
    showError(errorMessage(error));
  } finally {
    password.value = '';
    button.disabled = false;
  }
});

document.getElementById('logout').addEventListener('click', async () => {
  await callBackground('logout');
  render(null);
});

(async () => {
  const { lastCrmUrl } = await chrome.storage.local.get('lastCrmUrl');
  if (lastCrmUrl) {
    crmUrlInput.value = lastCrmUrl;
  }
  render(await callBackground('session'));
})();
