// Ordinary links remain usable before JS, with delayed JS, and on request failure.
document.addEventListener('click', async (event) => {
  const link = event.target.closest('a[data-al-load-more]');
  if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
  const root = link.closest('[data-al-listing]');
  if (!root || root.getAttribute('aria-busy') === 'true') return;
  event.preventDefault();
  root.setAttribute('aria-busy', 'true');
  const status = root.querySelector('.al-listing__status');
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 15000);
  try {
    const response = await fetch(link.href, { signal: controller.signal });
    if (!response.ok) throw new Error('Response failed');
    const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
    const next = [...doc.querySelectorAll('[data-al-listing]')].find((el) => el.dataset.alListing === root.dataset.alListing);
    const items = next?.querySelector('.al-listing__items');
    if (!items || !items.children.length) throw new Error('Missing content');
    const newItems = [...items.children];
    root.querySelector('.al-listing__items').append(...newItems);
    const currentNav = root.querySelector('.al-pagination');
    const nextNav = next.querySelector('.al-pagination');
    if (nextNav) currentNav.replaceWith(nextNav); else currentNav.remove();
    const firstLink = newItems[0].querySelector('a:not([aria-hidden="true"])');
    firstLink?.focus({ preventScroll: true });
    status.textContent = status.dataset.loaded;
  } catch {
    status.textContent = status.dataset.error;
    link.removeAttribute('data-al-load-more');
  } finally {
    clearTimeout(timeout);
    root.removeAttribute('aria-busy');
  }
});
