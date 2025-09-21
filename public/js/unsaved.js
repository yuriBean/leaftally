// public/js/unsaved-guard.js
(function () {
  const FORM_SELECTOR    = 'form:not([method="get"]):not([data-guard-ignore])';
  const SUBMIT_SELECTOR  = 'button[type="submit"],input[type="submit"],[data-guard-submit]';
  const LINK_SELECTOR    = 'a[href]:not([target="_blank"]):not([data-guard-ignore])';
  const SWAL_SELECTOR    = '.swal2-container, .swal2-popup';
  const MODAL_SELECTOR   = '.modal';
  const DIRTY_CLASS      = 'is-dirty';

  const inModal = (el) => !!(el && el.closest(MODAL_SELECTOR));

  function snapshotForm(form) {
    const fd = new FormData(form);

    form.querySelectorAll('input[type=checkbox]:not([disabled])').forEach(cb => {
      if (!fd.has(cb.name)) fd.append(cb.name, '');
    });

    const radiosByName = {};
    form.querySelectorAll('input[type=radio]:not([disabled])').forEach(r => {
      radiosByName[r.name] = radiosByName[r.name] || false;
      if (r.checked) radiosByName[r.name] = true;
    });
    Object.keys(radiosByName).forEach(name => {
      if (!radiosByName[name] && !fd.has(name)) fd.append(name, '');
    });

    const pairs = [];
    for (const [k, v] of fd.entries()) {
      if (k === '_token' || k === '_method') continue;
      if (v instanceof File) {
        pairs.push(`${k}:[FILE:${v.name}:${v.size}]`);
      } else {
        pairs.push(`${k}=${String(v)}`);
      }
    }
    pairs.sort();
    return pairs.join('&');
  }

  function markPristine(form) {
    form.dataset._guardSnapshot = snapshotForm(form);
    form.classList.remove(DIRTY_CLASS);
  }

  function isDirty(form) {
    if (form.hasAttribute('data-guard-ignore')) return false;
    if (inModal(form)) return false; // never guard forms inside modals
    const before = form.dataset._guardSnapshot || '';
    const now    = snapshotForm(form);
    const dirty  = before !== now;
    form.classList.toggle(DIRTY_CLASS, dirty);
    return dirty;
  }

  function anyDirty(root = document) {
    return Array.from(root.querySelectorAll(FORM_SELECTOR))
      .filter(f => !inModal(f))
      .some(isDirty);
  }

  function shouldBypass(elem) {
    if (!elem) return false;
    if (elem.closest(SWAL_SELECTOR)) return true;
    if (inModal(elem)) return true; // bypass everything inside modals
    if (elem.closest('[data-guard-ignore]')) return true;
    if (elem.closest('[data-guard-submitting="1"]')) return true;
    return false;
  }

  function isUiToggleLink(a) {
    if (!a) return false;
    const href = a.getAttribute('href') || '';
    if (href.startsWith('#')) return true;
    if (a.hasAttribute('data-bs-toggle') || a.hasAttribute('data-toggle')) return true;
    if ((a.getAttribute('role') || '').toLowerCase() === 'button') return true;
    if (a.hasAttribute('aria-expanded')) return true;
    const h = href.trim().toLowerCase();
    if (h === '#' || h === 'javascript:' || h.startsWith('javascript:void')) return true;
    return false;
  }

  function isExternalActionLink(a) {
    if (!a) return false;
    const href = a.getAttribute('href') || '';
    return href.startsWith('mailto:') || href.startsWith('tel:');
  }

  function attachToForm(form) {
    if (inModal(form)) return; // don't track modal forms
    markPristine(form);

    const recheck = () => isDirty(form);
    form.addEventListener('input', recheck, true);
    form.addEventListener('change', recheck, true);

    form.addEventListener('submit', () => {
      form.dataset.guardSubmitting = '1';
      form.setAttribute('data-guard-submitting', '1');
      markPristine(form);
    });
  }

  function guardMessage() {
    return 'You have unsaved changes. If you leave now, your changes will be lost.';
  }

  function confirmLeaveSwal() {
    if (window.Swal && typeof window.Swal.fire === 'function') {
      return window.Swal.fire({
        title: 'Discard changes?',
        text: guardMessage(),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Leave',
        cancelButtonText: 'Stay',
        reverseButtons: true,
        focusCancel: true,
        customClass: { confirmButton: 'btn bg-[#007c38] text-white  hover:bg-[#007c38]/90', cancelButton: 'btn bg-white border border-[#007c38] hover:border-[#007c38] text-[#007c38]' },
        buttonsStyling: false
      }).then(r => !!r.isConfirmed);
    }
    return Promise.resolve(window.confirm(guardMessage()));
  }

  // Intercept in-app LINK clicks (skip if inside modal)
  document.addEventListener('click', function (e) {
    if (e.target.closest(SWAL_SELECTOR)) return;

    const target = e.target.closest(LINK_SELECTOR);
    if (!target) return;
    if (shouldBypass(target)) return;
    if (isUiToggleLink(target) || isExternalActionLink(target)) return;

    const href = target.getAttribute('href');
    if (!href) return;

    try {
      const to = new URL(href, window.location.href);
      if (to.origin === window.location.origin &&
          to.pathname === window.location.pathname &&
          to.search === window.location.search) {
        return; // in-page anchor
      }
    } catch (_) {}

    if (anyDirty()) {
      e.preventDefault();
      e.stopImmediatePropagation();
      confirmLeaveSwal().then(ok => { if (ok) window.location.href = href; });
    }
  }, true);

  // Intercept “cancel/dismiss” buttons etc., but never inside modals
  document.addEventListener('click', function (e) {
    if (e.target.closest(SWAL_SELECTOR)) return;

    const submitBtn = e.target.closest(SUBMIT_SELECTOR);
    if (submitBtn) return; // allow submit

    const btn = e.target.closest('button,[data-dismiss="modal"],[data-bs-dismiss="modal"],[data-guard-leave]');
    if (!btn) return;
    if (shouldBypass(btn)) return;

    if (anyDirty()) {
      e.preventDefault();
      e.stopImmediatePropagation();
      confirmLeaveSwal().then(ok => {
        if (ok) {
          if (btn.hasAttribute('data-guard-leave')) history.back();
        }
      });
    }
  }, true);

  function init() {
    document.querySelectorAll(FORM_SELECTOR)
      .forEach(f => { if (!inModal(f)) attachToForm(f); });

    // If your app injects AJAX content, avoid tracking forms added inside modals
    document.addEventListener('ajax:popup:shown', function (e) {
      const root = e.detail?.root || document;
      root.querySelectorAll(FORM_SELECTOR)
        .forEach(f => { if (!inModal(f)) attachToForm(f); });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.UnsavedGuard = { snapshot: markPristine, isDirty, anyDirty };
})();
