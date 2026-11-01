// =============================================================
// Navigation
// =============================================================

document.addEventListener('DOMContentLoaded', () => {
  initMobileNav();
  initDropdowns();
});

// The toggle is only visible in the mobile layout.
function isMobileLayout() {
  const toggle = document.querySelector('.site-nav__toggle');
  return !!toggle && getComputedStyle(toggle).display !== 'none';
}

function focusAnchorTarget(link) {
  if (!link.hash || link.pathname !== window.location.pathname) return false;

  const target = document.getElementById(decodeURIComponent(link.hash.slice(1)));
  if (!target) return false;

  if (!target.hasAttribute('tabindex')) target.setAttribute('tabindex', '-1');
  target.focus();
  return true;
}

// --- Mobile nav toggle (off-canvas) --------------------------
function initMobileNav() {
  const toggle = document.querySelector('.site-nav__toggle');
  const nav = document.getElementById('main-nav');

  if (!toggle || !nav) return;

  const overlay = document.createElement('div');
  overlay.className = 'site-nav-overlay';
  document.body.appendChild(overlay);

  const mainContent = document.getElementById('content');
  const siteFooter = document.querySelector('.site-footer');

  function openNav() {
    toggle.setAttribute('aria-expanded', 'true');
    nav.classList.add('is-open');
    overlay.classList.add('is-visible');
    document.body.style.overflow = 'hidden';

    // Hide background from assistive technology
    mainContent?.setAttribute('inert', '');
    siteFooter?.setAttribute('inert', '');

    // Move focus into the menu
    const closeBtn = nav.querySelector('.site-nav__close');
    closeBtn?.focus();
  }

  function closeNav(returnFocus = true) {
    toggle.setAttribute('aria-expanded', 'false');
    nav.classList.remove('is-open');
    overlay.classList.remove('is-visible');
    document.body.style.overflow = '';

    // Restore background interactivity
    mainContent?.removeAttribute('inert');
    siteFooter?.removeAttribute('inert');

    if (returnFocus) toggle.focus();
  }

  const closeBtn = nav.querySelector('.site-nav__close');

  toggle.addEventListener('click', () => {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    expanded ? closeNav() : openNav();
  });

  closeBtn?.addEventListener('click', () => closeNav());
  overlay.addEventListener('click', () => closeNav());

  // An anchor link on the same page moves focus to its target.
  nav.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (!link || !nav.classList.contains('is-open')) return;

    closeNav(false);
    window.setTimeout(() => focusAnchorTarget(link), 0);
  });

  // Widened to desktop while open: unlock scrolling and the inert background.
  window.addEventListener('resize', () => {
    if (nav.classList.contains('is-open') && !isMobileLayout()) {
      closeNav(false);
    }
  });

  document.addEventListener('keydown', (e) => {
    // Escape in an open submenu is handled by initDropdowns().
    if (e.key === 'Escape' && nav.classList.contains('is-open') && !e.target.closest('.site-nav__item.is-open')) {
      closeNav();
      return;
    }

    // Focus trap: keep Tab inside the nav while it's open
    if (e.key === 'Tab' && nav.classList.contains('is-open')) {
      const focusable = Array.from(
        nav.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')
      ).filter((el) => el.offsetWidth > 0 || el.offsetHeight > 0);

      if (!focusable.length) return;

      const first = focusable[0];
      const last = focusable[focusable.length - 1];

      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  });
}

// --- Dropdown submenus ---------------------------------------
function initDropdowns() {
  const toggles = document.querySelectorAll('.site-nav__link--parent');

  function setOpen(btn, open) {
    btn.setAttribute('aria-expanded', String(open));
    btn.closest('.site-nav__item')?.classList.toggle('is-open', open);
  }

  toggles.forEach((btn) => {
    const item = btn.closest('.site-nav__item');

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const expanded = btn.getAttribute('aria-expanded') === 'true';

      toggles.forEach((other) => {
        if (other !== btn) setOpen(other, false);
      });

      setOpen(btn, !expanded);
    });

    // Desktop only: on mobile the submenus are an accordion.
    item?.addEventListener('focusout', (e) => {
      if (isMobileLayout() || item.contains(e.relatedTarget)) return;
      setOpen(btn, false);
    });
  });

  document.addEventListener('click', () => {
    toggles.forEach((btn) => setOpen(btn, false));
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;

    const openItem = document.activeElement?.closest('.site-nav__item.is-open');
    toggles.forEach((btn) => setOpen(btn, false));

    openItem?.querySelector(':scope > .site-nav__link--parent')?.focus();
  });
}
