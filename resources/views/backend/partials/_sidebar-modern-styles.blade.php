{{-- Modern styling for the admin sidebar. CSS-only; markup/behaviour unchanged. --}}
<style>
  .app-sidebar {
    border-inline-end: 1px solid var(--bs-border-color, #e8edf4);
    background: var(--bs-body-bg, #fff);
    box-shadow: 0 0 24px rgba(24, 39, 75, .05);
  }

  /* ---------- Brand ---------- */
  .app-sidebar .sidebar-brand {
    height: 4rem;
    border-bottom: 1px solid var(--bs-border-color, #e8edf4);
    background: transparent;
  }

  .app-sidebar .brand-link {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: 0 1.1rem;
    color: #17243b;
    font-weight: 700;
    letter-spacing: -.01em;
    text-decoration: none;
  }

  .app-sidebar .brand-link .brand-text { font-weight: 700; }

  /* ---------- Wrapper ---------- */
  .app-sidebar .sidebar-wrapper {
    padding: .5rem .75rem 1rem;
  }

  /* ---------- Menu items ---------- */
  .app-sidebar .sidebar-menu { padding-left: 0; list-style: none; }

  .app-sidebar .sidebar-menu .nav-item { margin: .1rem 0; }

  .app-sidebar .sidebar-menu .nav-link {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .55rem .75rem;
    border-radius: .65rem;
    color: #46566e;
    font-size: .9rem;
    font-weight: 500;
    transition: background-color .15s ease, color .15s ease;
  }

  .app-sidebar .sidebar-menu .nav-link p {
    margin: 0;
    flex: 1 1 auto;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .app-sidebar .sidebar-menu .nav-link:hover {
    background: rgba(53, 103, 214, .07);
    color: #2855bd;
  }

  .app-sidebar .sidebar-menu .nav-link.active {
    background: linear-gradient(145deg, #477be7, #2855bd);
    color: #fff;
    box-shadow: 0 4px 12px rgba(53, 103, 214, .22);
  }

  .app-sidebar .sidebar-menu .nav-link.active .nav-icon,
  .app-sidebar .sidebar-menu .nav-link.active .nav-arrow { color: #fff; }

  /* Parent of an open/active group */
  .app-sidebar .sidebar-menu .nav-item.menu-open > .nav-link:not(.active) {
    background: rgba(53, 103, 214, .06);
    color: #2855bd;
  }

  /* ---------- Icons ---------- */
  .app-sidebar .nav-icon {
    width: 1.25rem;
    flex: 0 0 1.25rem;
    color: #6b7891;
    font-size: 1.05rem;
    text-align: center;
    transition: color .15s ease;
  }

  .app-sidebar .sidebar-menu .nav-link:hover .nav-icon { color: #2855bd; }

  /* ---------- Expand arrow ---------- */
  .app-sidebar .nav-arrow {
    color: #9aa6b8;
    font-size: .8rem;
    transition: transform .2s ease;
  }

  /* ---------- Submenu ---------- */
  .app-sidebar .nav-treeview {
    margin: .15rem 0 .35rem;
    padding-left: .4rem;
    border-left: 1px solid rgba(53, 103, 214, .12);
    list-style: none;
  }

  .app-sidebar .nav-treeview .nav-link {
    padding: .45rem .75rem .45rem 1.5rem;
    font-size: .85rem;
    border-radius: .55rem;
  }

  .app-sidebar .nav-treeview .nav-icon { font-size: .95rem; }

  .app-sidebar .nav-treeview .nav-treeview { margin-top: 0; }

  /* ---------- Badge ---------- */
  .app-sidebar .nav-badge {
    flex: 0 0 auto;
    margin-left: auto;
    padding: .15rem .45rem;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 700;
    line-height: 1;
  }

  .app-sidebar .nav-link.active .nav-badge { background: rgba(255, 255, 255, .25); color: #fff; }

  /* ---------- Scrollbar polish ---------- */
  .app-sidebar .sidebar-wrapper::-webkit-scrollbar { width: 6px; }
  .app-sidebar .sidebar-wrapper::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, .25); border-radius: 999px; }

  /* ---------- Dark mode ---------- */
  [data-bs-theme="dark"] .app-sidebar {
    border-color: #29374d;
    background: #182233;
  }

  [data-bs-theme="dark"] .app-sidebar .sidebar-brand { border-color: #29374d; }
  [data-bs-theme="dark"] .app-sidebar .brand-link { color: #e4ebf6; }

  [data-bs-theme="dark"] .app-sidebar .sidebar-menu .nav-link { color: #b9c6d8; }
  [data-bs-theme="dark"] .app-sidebar .sidebar-menu .nav-link:hover { background: rgba(255, 255, 255, .05); color: #a9c1ff; }
  [data-bs-theme="dark"] .app-sidebar .sidebar-menu .nav-link.active { background: linear-gradient(145deg, #477be7, #2855bd); color: #fff; }
  [data-bs-theme="dark"] .app-sidebar .nav-icon { color: #93a2b8; }
  [data-bs-theme="dark"] .app-sidebar .sidebar-menu .nav-link:hover .nav-icon { color: #a9c1ff; }
  [data-bs-theme="dark"] .app-sidebar .nav-treeview { border-color: rgba(255, 255, 255, .08); }
  [data-bs-theme="dark"] .app-sidebar .nav-arrow { color: #7e8ca3; }
  [data-bs-theme="dark"] .app-sidebar .sidebar-menu .nav-item.menu-open > .nav-link:not(.active) { background: rgba(255, 255, 255, .05); color: #a9c1ff; }
</style>
