/*
 * Plain functions that read the LinkedIn profile the user is viewing. Loaded as a content
 * script before content.js and by tests/tests.html; no modules, no build.
 *
 * Images are never read: no photo, logo or background is captured, stored or shown.
 * When LinkedIn changes its markup, fix the selectors in LINKEDIN_SELECTORS: every field
 * tries its selectors in order and stays empty when none matches.
 */

const LINKEDIN_SELECTORS = {
  // The name is first read from the heading of the link to the profile (profileHeading())
  // the headline from the paragraph after it (paragraphsAfter()) and the location from the
  // block of the "Contact info" link (locationBeforeContactInfo()); these are fallbacks.
  name: ['main h1'],
  headline: [
    'main .pv-text-details__left-panel .text-body-medium',
    'main section .text-body-medium.break-words',
    'main [data-generated-suggestion-target]',
  ],
  location: [
    'main .pv-text-details__left-panel .text-body-small.inline',
    'main section .text-body-small.inline.t-black--light.break-words',
    'main section span.text-body-small.inline',
  ],
  currentCompany: [
    'main button[aria-label^="Current company"] div',
    'main button[aria-label^="Empresa actual"] div',
    'main [data-field="experience_company_logo"] span[aria-hidden="true"]',
  ],
  firstExperienceCompany: [
    '#experience ~ div ul > li .t-14.t-normal > span[aria-hidden="true"]',
    'section:has(#experience) ul > li .t-14.t-normal > span[aria-hidden="true"]',
  ],
  companyLink: [
    'main button[aria-label^="Current company"]',
    '#experience ~ div ul > li a[href*="/company/"]',
  ],
};

/**
 * Country names LinkedIn uses that differ from the CRM ones, as normalized text → code.
 * The CRM list (GET /api/country, from its countries library) covers the rest.
 */
const COUNTRY_ALIASES = {
  'united states of america': 'us',
  usa: 'us',
  'estados unidos de america': 'us',
  uk: 'gb',
  england: 'gb',
  scotland: 'gb',
  wales: 'gb',
  'northern ireland': 'gb',
  inglaterra: 'gb',
  turkiye: 'tr',
  turquia: 'tr',
  czechia: 'cz',
  'republica checa': 'cz',
  'the netherlands': 'nl',
  'south korea': 'kr',
  'corea del sur': 'kr',
};

function normalizeText(text) {
  return String(text || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/\s+/g, ' ')
    .trim()
    .toLowerCase();
}

/**
 * Index of country names → code from the CRM lists ({ en: [{id, name}], es: [...] }), so a
 * location in English or Spanish finds its country.
 */
function countryIndex(countryLists) {
  const index = new Map(Object.entries(COUNTRY_ALIASES));
  Object.values(countryLists || {}).forEach((countries) => {
    (countries || []).forEach((country) => index.set(normalizeText(country.name), country.id));
  });
  return index;
}

/**
 * Splits "City, Province, Country", "City, Country" or "Country" using the countryIndex().
 * When the last part is not a known country it is kept as province or city and the country
 * stays empty for the user to pick.
 */
function splitLocation(text, index) {
  const parts = String(text || '').split(',').map((part) => part.trim()).filter(Boolean);
  const location = { city: '', province: '', countryId: '' };
  if (parts.length === 0) {
    return location;
  }

  const code = index?.get(normalizeText(parts.at(-1))) || '';
  if (code) {
    location.countryId = code;
    parts.pop();
  }
  if (parts.length > 0) {
    location.city = parts.shift();
  }
  if (parts.length > 0) {
    location.province = parts.join(', ');
  }
  return location;
}

/**
 * Profile path "/in/<slug>" of a LinkedIn member URL, lower case and decoded, the same for
 * every way of writing it; null when it is not a profile URL. Same rules and cases as
 * App\ValueObjects\LinkedinProfileUrl in the CRM.
 */
function linkedinProfilePath(value) {
  let text = String(value || '').trim();
  if (text === '') {
    return null;
  }
  if (!/^[a-z][a-z0-9+.-]*:\/\//i.test(text)) {
    text = 'https://' + text;
  }

  let url;
  try {
    url = new URL(text);
  } catch (error) {
    return null;
  }
  const host = url.hostname.toLowerCase();
  if (host !== 'linkedin.com' && !host.endsWith('.linkedin.com')) {
    return null;
  }

  let path;
  try {
    path = decodeURIComponent(url.pathname);
  } catch (error) {
    return null;
  }
  const match = /^\/in\/([^/]+)\/?$/u.exec(path);
  return match ? '/in/' + match[1].toLowerCase() : null;
}

/**
 * Canonical URL saved in the CRM for a profile, percent-encoded as LinkedIn writes it in its
 * links: the CRM `url` validation rejects symbols such as "®" unencoded.
 */
function linkedinProfileUrl(value) {
  const path = linkedinProfilePath(value);
  return path ? 'https://www.linkedin.com/in/' + encodeURIComponent(path.slice('/in/'.length)) : '';
}

function firstText(root, selectors) {
  for (const selector of selectors) {
    let element = null;
    try {
      element = root.querySelector(selector);
    } catch (error) {
      element = null;
    }
    const text = element ? element.textContent.replace(/\s+/g, ' ').trim() : '';
    if (text) {
      return text;
    }
  }
  return '';
}

function firstCompanyUrl(root) {
  for (const selector of LINKEDIN_SELECTORS.companyLink) {
    let element = null;
    try {
      element = root.querySelector(selector);
    } catch (error) {
      element = null;
    }
    const link = element?.closest('a');
    const match = link?.href ? /^https:\/\/www\.linkedin\.com\/company\/[^/?#]+/.exec(link.href) : null;
    if (match) {
      return match[0] + '/';
    }
  }
  return '';
}

function capitalize(word) {
  return word.charAt(0).toLocaleUpperCase() + word.slice(1).toLocaleLowerCase();
}

/**
 * Display name without credentials or symbols: "Martín GÓMEZ, PMP®" → "Martín Gómez".
 * Drops what follows the first comma, keeps letters, spaces, hyphens, apostrophes and dots,
 * and capitalizes words written in capitals.
 */
function cleanName(text) {
  return String(text || '')
    .split(',')[0]
    .replace(/[^\p{L}\p{M}\s'.-]/gu, ' ')
    .replace(/\s+/g, ' ')
    .trim()
    .split(' ')
    .filter(Boolean)
    .map((word) => (word.length > 1 && word === word.toLocaleUpperCase() ? capitalize(word) : word))
    .join(' ');
}

/**
 * Name from the profile URL when the page does not show it, like the CRM contact form:
 * "/in/martín-gómez-pmp®-1a2b3c4d" → "Martín Gómez". Words with digits (the LinkedIn id) or
 * symbols (credentials such as "pmp®") are dropped.
 */
function nameFromProfileUrl(url) {
  const path = linkedinProfilePath(url);
  if (!path) {
    return '';
  }
  return path.slice('/in/'.length)
    .split('-')
    .filter((word) => /^[\p{L}\p{M}']+$/u.test(word))
    .map(capitalize)
    .join(' ');
}

/** Heading of the link to the profile itself, where LinkedIn shows the member name. */
function profileHeading(root, pageUrl) {
  const path = linkedinProfilePath(pageUrl);
  if (!path) {
    return null;
  }
  for (const link of root.querySelectorAll('a[href*="/in/"]')) {
    const heading = link.querySelector('h1, h2');
    if (heading?.textContent.trim() && linkedinProfilePath(link.href) === path) {
      return heading;
    }
  }
  return null;
}

/** Connection degree or separator shown next to the name: "· 1st", "• 2nd", "3rd+", "2º", "·". */
function isNameDecoration(text) {
  return /^[·•|-]/.test(text) || /^(?:\d+(?:st|nd|rd|th|º|er|do|ro)\+?)$/i.test(text);
}

/**
 * Texts of the paragraphs that follow the name heading in the page, in order, skipping the
 * connection degree. LinkedIn shows the headline (job title and company) in the first one;
 * its class names change, the order does not.
 */
function paragraphsAfter(root, element, limit) {
  const texts = [];
  for (const paragraph of root.querySelectorAll('p')) {
    if (!(element.compareDocumentPosition(paragraph) & Node.DOCUMENT_POSITION_FOLLOWING)) {
      continue;
    }
    const text = paragraph.textContent.replace(/\s+/g, ' ').trim();
    if (text && !isNameDecoration(text)) {
      texts.push(text);
    }
    if (texts.length >= limit) {
      break;
    }
  }
  return texts;
}

/**
 * Location shown next to the "Contact info" link: the first paragraph of the block that holds
 * the link, skipping separators such as "·". The link keeps its URL when class names change.
 */
function locationBeforeContactInfo(root) {
  for (const link of root.querySelectorAll('a[href*="/overlay/contact-info"]')) {
    const linkParagraph = link.closest('p');
    const text = linkParagraph?.parentElement ? firstParagraphBefore(linkParagraph) : '';
    if (text) {
      return text;
    }
  }
  return '';
}

/** Text of the first paragraph before this one in its block that is not a separator. */
function firstParagraphBefore(paragraph) {
  for (const sibling of paragraph.parentElement.children) {
    if (sibling === paragraph) {
      break;
    }
    const text = sibling.tagName === 'P' ? sibling.textContent.replace(/\s+/g, ' ').trim() : '';
    if (text && !/^[·•|-]+$/.test(text)) {
      return text;
    }
  }
  return '';
}

/** "Jane Doe" → Jane / Doe: the first word is the first name, the rest the last name. */
function splitName(fullName) {
  const words = String(fullName || '').replace(/\s+/g, ' ').trim().split(' ').filter(Boolean);
  return { firstName: words.shift() || '', lastName: words.join(' ') };
}

/**
 * "Head of Sales at Acme", "Director en Acme", "CTO @ Acme" or "CTO | Acme" → job title and
 * company. Only the first "|" segment is split on at/en/@, so "CTO at Acme | Mentor" keeps
 * "Acme". When the company is already known, a headline ending with it just drops it.
 */
function splitHeadline(headline, knownCompany) {
  const text = String(headline || '').replace(/\s+/g, ' ').trim();
  const company = String(knownCompany || '').trim();
  if (company) {
    const escaped = company.replace(/[.*+?^${}()|[\]\\]/g, String.raw`\$&`);
    const suffix = new RegExp(String.raw`(?:\s+(?:at|en)\s+|\s*[@|]\s*)` + escaped + String.raw`\s*$`, 'i');
    if (suffix.test(text)) {
      return { jobTitle: text.replace(suffix, '').trim(), company };
    }
  }

  const segments = text.split('|').map((segment) => segment.trim()).filter(Boolean);
  const first = segments[0] || '';
  const split = splitAtLastWord(first) || splitAtFirstAt(first);
  if (split) {
    return split;
  }
  if (segments.length > 1) {
    return { jobTitle: first, company: segments[1] };
  }
  return { jobTitle: text, company: '' };
}

/** "Head of Sales at Acme" → split at the last " at " or " en ", without backtracking regexes. */
function splitAtLastWord(text) {
  const separators = [...text.matchAll(/\s(?:at|en)\s/gi)];
  const last = separators.at(-1);
  if (!last) {
    return null;
  }
  const jobTitle = text.slice(0, last.index).trim();
  const company = text.slice(last.index + last[0].length).trim();
  return jobTitle && company ? { jobTitle, company } : null;
}

/** "CTO @ Acme" → split at the first "@". */
function splitAtFirstAt(text) {
  const at = text.indexOf('@');
  const jobTitle = at > 0 ? text.slice(0, at).trim() : '';
  const company = at > 0 ? text.slice(at + 1).trim() : '';
  return jobTitle && company ? { jobTitle, company } : null;
}

function cleanCompany(text) {
  return String(text || '').split(' · ')[0].trim();
}

/**
 * Reads the profile shown in the page: name, job title, company, location text and profile
 * URL. The panel splits the location with the CRM countries. Missing parts are returned
 * empty. Never reads images.
 */
function readLinkedinProfile(root, pageUrl) {
  const field = (name) => {
    try {
      return firstText(root, LINKEDIN_SELECTORS[name]);
    } catch (error) {
      return '';
    }
  };

  let heading = null;
  let following = [];
  try {
    heading = profileHeading(root, pageUrl);
    following = heading ? paragraphsAfter(root, heading, 1) : [];
  } catch (error) {
    // A markup change must not break the form: fall back to the selectors below.
    console.debug('LinkedIn profile heading not read', error);
    heading = null;
    following = [];
  }
  const shownName = heading ? heading.textContent.replace(/\s+/g, ' ').trim() : field('name');
  const name = splitName(cleanName(shownName) || nameFromProfileUrl(pageUrl));
  const currentCompany = cleanCompany(field('currentCompany'));
  const headline = splitHeadline(following[0] || field('headline'), currentCompany);
  const company = currentCompany ||
    headline.company ||
    cleanCompany(field('firstExperienceCompany'));

  return {
    firstName: name.firstName,
    lastName: name.lastName,
    jobTitle: headline.jobTitle,
    company,
    companyLinkedin: firstCompanyUrl(root),
    location: (() => {
      try {
        return locationBeforeContactInfo(root);
      } catch (error) {
        return '';
      }
    })() || field('location'),
    linkedin: linkedinProfileUrl(pageUrl),
  };
}
