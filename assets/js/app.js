/**
 * Government Workflow OS — Core Client JavaScript
 * Sidebar collapsible, mobile drawer, dropdowns, keyboard shortcuts, modal controls
 */

document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
  initDropdowns();
  initSearchShortcut();
  initPasswordToggles();
  initModals();
});

/**
 * Sidebar collapsible state & mobile drawer management
 */
function initSidebar() {
  const sidebar = document.getElementById('app-sidebar');
  const collapseBtn = document.getElementById('sidebar-collapse-btn');
  const mobileToggleBtn = document.getElementById('mobile-nav-toggle');
  const backdrop = document.getElementById('sidebar-backdrop');

  if (!sidebar) return;

  // Restore desktop collapsed preference
  const isCollapsed = localStorage.getItem('gov_sidebar_collapsed') === 'true';
  if (isCollapsed && window.innerWidth > 1024) {
    sidebar.classList.add('collapsed');
  }

  // Desktop Collapse Toggle
  if (collapseBtn) {
    collapseBtn.addEventListener('click', (e) => {
      e.preventDefault();
      sidebar.classList.toggle('collapsed');
      const nowCollapsed = sidebar.classList.contains('collapsed');
      localStorage.setItem('gov_sidebar_collapsed', nowCollapsed);
    });
  }

  // Mobile Drawer Open
  if (mobileToggleBtn) {
    mobileToggleBtn.addEventListener('click', (e) => {
      e.preventDefault();
      sidebar.classList.add('mobile-open');
      if (backdrop) {
        backdrop.classList.add('active');
      }
    });
  }

  // Mobile Backdrop Click to Close
  if (backdrop) {
    backdrop.addEventListener('click', () => {
      sidebar.classList.remove('mobile-open');
      backdrop.classList.remove('active');
    });
  }

  // Close mobile drawer on ESC key
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (sidebar.classList.contains('mobile-open')) {
        sidebar.classList.remove('mobile-open');
        if (backdrop) backdrop.classList.remove('active');
      }
      closeAllDropdowns();
      closeActiveModal();
    }
  });

  // Handle window resizing
  window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
      sidebar.classList.remove('mobile-open');
      if (backdrop) backdrop.classList.remove('active');
    }
  });
}

/**
 * Dropdown Menu Controller (User Menu, Notifications Menu)
 */
function initDropdowns() {
  const dropdownTriggers = document.querySelectorAll('[data-dropdown-trigger]');

  dropdownTriggers.forEach(trigger => {
    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      const targetId = trigger.getAttribute('data-dropdown-trigger');
      const menu = document.getElementById(targetId);

      if (!menu) return;

      const isOpen = menu.classList.contains('active');
      closeAllDropdowns();

      if (!isOpen) {
        menu.classList.add('active');
      }
    });
  });

  // Click outside to close
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown-menu') && !e.target.closest('[data-dropdown-trigger]')) {
      closeAllDropdowns();
    }
  });
}

function closeAllDropdowns() {
  document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
    menu.classList.remove('active');
  });
}

/**
 * Global search keyboard shortcut (Ctrl+K or Cmd+K)
 */
function initSearchShortcut() {
  const searchInput = document.getElementById('header-search-input');
  if (!searchInput) return;

  window.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      searchInput.focus();
      searchInput.select();
    }
  });
}

/**
 * Password Visibility Toggle on login forms
 */
function initPasswordToggles() {
  const toggleBtns = document.querySelectorAll('[data-toggle-password]');

  toggleBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const targetInputId = btn.getAttribute('data-toggle-password');
      const input = document.getElementById(targetInputId);
      if (!input) return;

      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = `
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
          </svg>
        `;
      } else {
        input.type = 'password';
        btn.innerHTML = `
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
        `;
      }
    });
  });
}

/**
 * Modal Dialog Controller
 */
function initModals() {
  // Modal open triggers
  document.querySelectorAll('[data-modal-open]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modalId = btn.getAttribute('data-modal-open');
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  // Modal close triggers
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      closeActiveModal();
    });
  });

  // Click outside dialog to close
  document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', (e) => {
      if (e.target === backdrop) {
        closeActiveModal();
      }
    });
  });
}

function closeActiveModal() {
  document.querySelectorAll('.modal-backdrop.active').forEach(modal => {
    modal.classList.remove('active');
  });
  document.body.style.overflow = '';
}

/**
 * Quick autofill demo credentials on login
 */
function autofillDemoCredentials(email = 'juan.delacruz@pgov.ph', password = 'Password123!') {
  const emailInput = document.getElementById('login-email');
  const passInput = document.getElementById('login-password');
  if (emailInput && passInput) {
    emailInput.value = email;
    passInput.value = password;
    emailInput.focus();
  }
}
window.autofillDemoCredentials = autofillDemoCredentials;
