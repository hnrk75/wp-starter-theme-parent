// =============================================================
// Navigation
// =============================================================

document.addEventListener('DOMContentLoaded', () => {
  initMobileNav();
  initDropdowns();
});

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

  closeBtn?.addEventListener('click', closeNav);
  overlay.addEventListener('click', closeNav);

  // Close when clicking any link inside the nav (including anchor links)
  nav.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (link) closeNav();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && nav.classList.contains('is-open')) {
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

  toggles.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const expanded = btn.getAttribute('aria-expanded') === 'true';

      toggles.forEach((other) => {
        if (other !== btn) {
          other.setAttribute('aria-expanded', 'false');
          other.closest('.site-nav__item')?.classList.remove('is-open');
        }
      });

      btn.setAttribute('aria-expanded', String(!expanded));
      btn.closest('.site-nav__item')?.classList.toggle('is-open', !expanded);
    });
  });

  document.addEventListener('click', () => {
    toggles.forEach((btn) => {
      btn.setAttribute('aria-expanded', 'false');
      btn.closest('.site-nav__item')?.classList.remove('is-open');
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    toggles.forEach((btn) => {
      btn.setAttribute('aria-expanded', 'false');
      btn.closest('.site-nav__item')?.classList.remove('is-open');
    });
  });
}
