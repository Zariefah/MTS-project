/*!
 * @package LeAF CI4
 * @since 4.0.0
 * @author George Lewe <george@lewe.com>
 * @copyright Copyright (c) 2024 by George Lewe
 * @link https://www.lewe.com
 */
function setSidebarExpandedState(expanded, persist) {
  const sidebarToggle = document.querySelector('.sidebar-toggle');
  const eleSidebar = document.querySelector('#sidebar');
  const eleNavbar = document.querySelector('#navbar');
  const eleMain = document.querySelector('#main');
  const icon = sidebarToggle ? sidebarToggle.querySelector('.sidebar-toggle-icon') : null;

  if (eleSidebar) {
    if (expanded) {
      eleSidebar.classList.add('expand');
    } else {
      eleSidebar.classList.remove('expand');
    }
  }

  if (eleNavbar) {
    if (expanded) {
      eleNavbar.classList.add('expand');
    } else {
      eleNavbar.classList.remove('expand');
    }
  }

  if (eleMain) {
    if (expanded) {
      eleMain.classList.add('expand');
    } else {
      eleMain.classList.remove('expand');
    }
  }

  if (icon) {
    icon.classList.toggle('bi-arrow-left-square-fill', expanded);
    icon.classList.toggle('bi-arrow-right-square-fill', !expanded);
  }

  try {
    if (persist) {
      localStorage.setItem('sidebarExpanded', expanded ? '1' : '0');
    }
  } catch (e) {
    // ignore storage errors (e.g., private mode restrictions)
  }
}

function initSidebarToggle() {
  const sidebarToggle = document.querySelector('.sidebar-toggle');
  if (!sidebarToggle) {
    return;
  }

  let stored;
  try {
    stored = localStorage.getItem('sidebarExpanded');
  } catch (e) {
    stored = null;
  }

  // Open by default; only stay closed when user explicitly collapsed ('0').
  // HTML is server-rendered with .expand so layout is correct before this runs.
  const expanded = stored !== '0';
  setSidebarExpandedState(expanded, false);

  sidebarToggle.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const eleSidebar = document.querySelector('#sidebar');
    const isExpanded = eleSidebar && eleSidebar.classList.contains('expand');
    setSidebarExpandedState(!isExpanded, true);
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initSidebarToggle);
} else {
  initSidebarToggle();
}
