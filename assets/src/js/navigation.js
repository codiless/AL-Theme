for (const header of document.querySelectorAll('[data-al-navigation]')) {
  const trigger = header.querySelector('.site-menu-toggle');
  const panel = header.querySelector('.site-navigation');
  if (!trigger || !panel) continue;
  const mobile = window.matchMedia('(max-width: 899px)');
  const submenus = [...header.querySelectorAll('.al-submenu-toggle')];
  const target = (button) => document.getElementById(button.getAttribute('aria-controls'));
  const disclose = (button, open) => {
    const list = target(button);
    if (!list) return;
    button.setAttribute('aria-expanded', String(open));
    list.hidden = !open;
    if (!open) {
      for (const nested of list.querySelectorAll('.al-submenu-toggle')) {
        nested.setAttribute('aria-expanded', 'false');
        if (target(nested)) target(nested).hidden = true;
      }
    }
  };
  const closeSubmenus = () => submenus.forEach((button) => disclose(button, false));
  const setMobileOpen = (open) => {
    if (!open && mobile.matches && panel.contains(document.activeElement)) trigger.focus();
    trigger.setAttribute('aria-expanded', String(open));
    panel.hidden = mobile.matches && !open;
    if (!open) closeSubmenus();
  };
  header.classList.add('site-header--enhanced');
  submenus.forEach((button) => disclose(button, false));
  setMobileOpen(false);
  trigger.addEventListener('click', () => setMobileOpen(trigger.getAttribute('aria-expanded') !== 'true'));
  for (const button of submenus) {
    button.addEventListener('click', () => {
      const open = button.getAttribute('aria-expanded') !== 'true';
      // Close siblings, retaining the ancestor chain for deeper dropdowns.
      for (const sibling of submenus) {
        if (sibling !== button && sibling.parentElement.parentElement === button.parentElement.parentElement) disclose(sibling, false);
      }
      disclose(button, open);
    });
  }
  header.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    const open = submenus.filter((button) => button.getAttribute('aria-expanded') === 'true' && button.parentElement.contains(event.target)).pop();
    if (open) { disclose(open, false); open.focus(); event.preventDefault(); }
    else if (mobile.matches && trigger.getAttribute('aria-expanded') === 'true') { setMobileOpen(false); trigger.focus(); event.preventDefault(); }
  });
  document.addEventListener('click', (event) => {
    if (!header.contains(event.target)) setMobileOpen(false);
    else if (mobile.matches && event.target.closest('a[href]')) setMobileOpen(false);
  });
  header.addEventListener('focusout', (event) => {
    if (!header.contains(event.relatedTarget)) closeSubmenus();
  });
  mobile.addEventListener('change', () => {
    const wasFocused = panel.contains(document.activeElement);
    setMobileOpen(false);
    if (mobile.matches && wasFocused) trigger.focus();
  });
}
