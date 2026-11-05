/*
 * Shared by the extension pages: fills every [data-i18n] element with its _locales message
 * and maps CRM error codes to readable messages.
 */

function t(key, substitutions) {
  return chrome.i18n.getMessage(key, substitutions) || key;
}

function translatePage() {
  document.documentElement.lang = chrome.i18n.getUILanguage();
  document.querySelectorAll('[data-i18n]').forEach((element) => {
    element.textContent = t(element.dataset.i18n);
  });
  document.querySelectorAll('[data-i18n-placeholder]').forEach((element) => {
    element.placeholder = t(element.dataset.i18nPlaceholder);
  });
}

const ERROR_MESSAGES = {
  invalid_url: 'errorInvalidUrl',
  insecure_url: 'errorInsecureUrl',
  invalid_credentials: 'errorInvalidCredentials',
  unreachable: 'errorUnreachable',
  permission_denied: 'errorPermissionDenied',
  too_many_attempts: 'errorTooManyAttempts',
  logged_out: 'errorLoggedOut',
  session_expired: 'errorSessionExpired',
  inactive: 'errorInactive',
  forbidden: 'errorForbidden',
  validation: 'errorValidation',
};

function errorMessage(error) {
  return t(ERROR_MESSAGES[error && error.code] || 'errorUnexpected');
}

/** Sends a message to background.js and resolves with its data or rejects with its error. */
async function callBackground(type, payload) {
  const response = await chrome.runtime.sendMessage({ type, payload });
  if (!response) {
    throw { code: 'unexpected' };
  }
  if (!response.ok) {
    throw response.error;
  }
  return response.data;
}

translatePage();
