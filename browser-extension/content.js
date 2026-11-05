/*
 * Runs on LinkedIn. Shows the "Add to CRM" button only on member profiles (/in/<slug>), reads
 * the profile only when the user clicks it and opens the contact panel (an extension page in
 * an iframe) with it. It never sees the API token, never calls the CRM and never reads images.
 */

(() => {
  const EXTENSION_ORIGIN = new URL(chrome.runtime.getURL('')).origin;
  const PANEL_URL = chrome.runtime.getURL('panel.html');
  const HOST_ID = 'prospero-flow-crm-extension';

  let host = null;
  let shadow = null;
  let panel = null;
  let lastUrl = '';

  function isProfilePage() {
    return linkedinProfilePath(location.href) !== null;
  }

  function createHost() {
    host = document.createElement('div');
    host.id = HOST_ID;
    shadow = host.attachShadow({ mode: 'closed' });
    shadow.innerHTML = `
      <style>
        .button {
          position: fixed; right: 24px; bottom: 24px; z-index: 2147483646;
          padding: 10px 16px; border: 0; border-radius: 20px;
          font: 600 14px/1.2 system-ui, sans-serif; color: #fff; background: #0b5cad;
          box-shadow: 0 2px 8px rgba(0, 0, 0, .25); cursor: pointer;
        }
        .notice {
          position: fixed; right: 24px; bottom: 72px; z-index: 2147483646; max-width: 280px;
          padding: 10px 12px; border-radius: 6px;
          font: 13px/1.4 system-ui, sans-serif; color: #fff; background: #1f2933;
        }
        iframe {
          position: fixed; top: 64px; right: 16px; z-index: 2147483647;
          width: 400px; max-width: calc(100vw - 32px); height: calc(100vh - 80px);
          border: 0; border-radius: 8px; background: #fff;
          box-shadow: 0 4px 24px rgba(0, 0, 0, .3);
        }
      </style>
      <button class="button" type="button"></button>
      <div class="notice" hidden></div>`;
    const button = shadow.querySelector('.button');
    button.textContent = chrome.i18n.getMessage('addToCrm');
    button.addEventListener('click', onAddToCrm);
    document.documentElement.appendChild(host);
  }

  function showNotice(message) {
    const notice = shadow.querySelector('.notice');
    notice.textContent = message;
    notice.hidden = false;
    setTimeout(() => {
      notice.hidden = true;
    }, 6000);
  }

  function closePanel() {
    if (panel) {
      panel.remove();
      panel = null;
    }
  }

  async function onAddToCrm() {
    let session = null;
    try {
      const response = await chrome.runtime.sendMessage({ type: 'session' });
      session = response?.ok ? response.data : null;
    } catch (error) {
      // The extension was reloaded or updated: the page keeps the old script until refreshed.
      console.debug('Extension not reachable', error);
      session = null;
    }
    if (!session) {
      showNotice(chrome.i18n.getMessage('errorLoggedOut'));
      return;
    }

    const profile = readLinkedinProfile(document, location.href);
    closePanel();
    panel = document.createElement('iframe');
    panel.src = PANEL_URL;
    panel.title = chrome.i18n.getMessage('extName');
    panel.addEventListener('load', () => {
      panel.contentWindow.postMessage({ type: 'profile', profile }, EXTENSION_ORIGIN);
    }, { once: true });
    shadow.appendChild(panel);
  }

  window.addEventListener('message', (event) => {
    if (!panel || event.source !== panel.contentWindow || event.origin !== EXTENSION_ORIGIN) {
      return;
    }
    if (event.data?.type === 'close') {
      closePanel();
    }
  });

  /** LinkedIn is a single page app: re-check the URL whenever it may have changed. */
  function update() {
    if (location.href === lastUrl) {
      return;
    }
    lastUrl = location.href;
    closePanel();
    if (isProfilePage()) {
      if (!host) {
        createHost();
      }
      host.hidden = false;
    } else if (host) {
      host.hidden = true;
    }
  }

  if (window.navigation) {
    window.navigation.addEventListener('navigatesuccess', update);
  }
  window.addEventListener('popstate', update);
  new MutationObserver(update).observe(document.head || document.documentElement, {
    childList: true,
    subtree: true,
    characterData: true,
  });
  update();
})();
