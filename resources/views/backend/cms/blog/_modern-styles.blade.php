{{-- Shared modern styling for admin blog screens. Pushed onto the `styles` stack. --}}
<style>
  .cms-blog {
    --cb-accent: #3567d6;
    --cb-ink: #17243b;
    --cb-muted: #68778d;
    --cb-line: #e8edf4;
  }

  /* ---------- Hero ---------- */
  .cms-blog__hero {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    overflow: hidden;
    padding: 1.5rem 1.75rem;
    border: 1px solid #dfe8fb;
    border-radius: 1rem;
    background: linear-gradient(115deg, #f3f7ff 0%, #fff 58%, #f5f8ff 100%);
    box-shadow: 0 8px 26px rgba(34, 65, 120, .06);
  }

  .cms-blog__hero::after {
    position: absolute;
    top: -5.5rem;
    right: 7rem;
    width: 14rem;
    height: 14rem;
    border: 1.5rem solid rgba(53, 103, 214, .045);
    border-radius: 50%;
    content: "";
    pointer-events: none;
  }

  .cms-blog__heading {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
  }

  .cms-blog__hero-icon {
    display: grid;
    width: 3.25rem;
    height: 3.25rem;
    flex: 0 0 3.25rem;
    place-items: center;
    border-radius: .95rem;
    background: linear-gradient(145deg, #477be7, #2855bd);
    box-shadow: 0 8px 18px rgba(53, 103, 214, .23);
    color: #fff;
    font-size: 1.35rem;
  }

  .cms-blog__title {
    margin: 0;
    color: var(--cb-ink);
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: -.02em;
  }

  .cms-blog__actions {
    position: relative;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
  }

  /* ---------- Buttons ---------- */
  .cms-blog__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    min-height: 2.5rem;
    padding-inline: 1rem;
    border: 1px solid transparent;
    border-radius: .65rem;
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
  }

  .cms-blog__btn--primary { background: var(--cb-accent); color: #fff; box-shadow: 0 4px 10px rgba(53, 103, 214, .18); }
  .cms-blog__btn--primary:hover { background: #2855bd; color: #fff; }

  .cms-blog__btn--ghost { border-color: #dce6fa; background: rgba(255, 255, 255, .86); color: #315ebd; }
  .cms-blog__btn--ghost:hover { background: #eaf0fe; color: #2855bd; }

  .cms-blog__btn--subtle { border-color: var(--cb-line); background: #fff; color: #5f6e82; }
  .cms-blog__btn--subtle:hover { background: #f4f7ff; color: #315ebd; }

  .cms-blog__btn--danger { border-color: #f3cdca; background: #fff; color: #b42318; }
  .cms-blog__btn--danger:hover { background: #fdeceb; color: #9a1f17; }

  .cms-blog__btn--secondary { border-color: var(--cb-line); background: #f4f6fa; color: #46566e; }
  .cms-blog__btn--secondary:hover { background: #e9edf4; color: #28364d; }

  /* ---------- Card ---------- */
  .cms-blog__card {
    overflow: hidden;
    border: 1px solid var(--cb-line);
    border-radius: 1rem;
    background: #fff;
    box-shadow: 0 6px 24px rgba(24, 39, 75, .045);
  }

  .cms-blog__card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.05rem 1.25rem;
    border-bottom: 1px solid var(--cb-line);
  }

  .cms-blog__card-title {
    display: flex;
    align-items: center;
    gap: .55rem;
    margin: 0;
    color: var(--cb-ink);
    font-size: .98rem;
    font-weight: 700;
  }

  .cms-blog__card-title i { color: var(--cb-accent); }

  .cms-blog__card-body { padding: 1.25rem; }

  .cms-blog__card-foot {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: .5rem;
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--cb-line);
    background: #fafbfd;
  }

  /* ---------- Filters ---------- */
  .cms-blog__filters { margin-top: 1.15rem; padding: 1.1rem 1.25rem 1.2rem; }

  .cms-blog__filters-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: .85rem;
  }

  .cms-blog__filters-title {
    display: flex;
    align-items: center;
    gap: .55rem;
    margin: 0;
    color: var(--cb-ink);
    font-size: .95rem;
    font-weight: 650;
  }

  .cms-blog__filters-title i { color: var(--cb-accent); }

  .cms-blog__clear {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    color: #65748b;
    font-size: .825rem;
    font-weight: 600;
    text-decoration: none;
  }

  .cms-blog__clear:hover { color: var(--cb-accent); }

  .cms-blog__filter-form { display: flex; flex-wrap: wrap; gap: .65rem; align-items: center; }

  .cms-blog__filter-form .form-control,
  .cms-blog__filter-form .form-select { min-height: 2.6rem; border-color: #e0e6ef; border-radius: .65rem; box-shadow: none; }

  .cms-blog__filter-form .form-control { min-width: 200px; flex: 1 1 220px; }

  .cms-blog__filter-form .form-control:focus,
  .cms-blog__filter-form .form-select:focus { border-color: #85a8f3; box-shadow: 0 0 0 .2rem rgba(53, 103, 214, .1); }

  /* ---------- Results ---------- */
  .cms-blog__results { margin-top: 1.15rem; }

  .cms-blog__results-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--cb-line);
  }

  .cms-blog__range { color: var(--cb-muted); font-size: .82rem; }

  .cms-blog__table-wrap { overflow-x: auto; }

  .cms-blog__table { min-width: 760px; margin: 0; vertical-align: middle; }

  .cms-blog__table thead th {
    padding: .8rem 1rem;
    border-bottom: 1px solid var(--cb-line);
    background: #f8faff;
    color: #77859a;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .cms-blog__table tbody td {
    padding: .9rem 1rem;
    border-color: #eef1f6;
    color: #344258;
    font-size: .84rem;
  }

  .cms-blog__table tbody tr { transition: background-color .15s ease; }
  .cms-blog__table tbody tr:hover { background: #f8faff; }

  .cms-blog__num { font-variant-numeric: tabular-nums; }

  .cms-blog__name { display: block; color: #28364d; font-weight: 650; }
  .cms-blog__sub { display: block; margin-top: .15rem; color: #8a97aa; font-size: .75rem; }

  .cms-blog__slug {
    display: inline-block;
    padding: .12rem .4rem;
    border-radius: .35rem;
    background: #f3f5f9;
    color: #7a8699;
    font-size: .72rem;
  }

  .cms-blog__muted { color: #a3adbd; }

  .cms-blog__row-actions { display: flex; flex-wrap: wrap; gap: .4rem; white-space: nowrap; }

  /* ---------- Status pill ---------- */
  .cms-blog__status {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .3rem .6rem;
    border: 1px solid transparent;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .02em;
    white-space: nowrap;
  }

  .cms-blog__status::before { width: .45rem; height: .45rem; border-radius: 50%; background: currentColor; content: ""; }
  .cms-blog__status--success { border-color: #bce8d2; background: #e8f6ef; color: #16794c; }
  .cms-blog__status--danger { border-color: #f3cdca; background: #fdeceb; color: #b42318; }

  /* ---------- Thumbnail ---------- */
  .cms-blog__thumb {
    width: 72px;
    height: 48px;
    border-radius: .5rem;
    object-fit: cover;
    border: 1px solid var(--cb-line);
    background: #f3f5f9;
  }

  .cms-blog__thumb-placeholder {
    display: grid;
    width: 72px;
    height: 48px;
    place-items: center;
    border-radius: .5rem;
    border: 1px dashed #d3dcea;
    background: #f8fafd;
    color: #b3bcc9;
  }

  /* ---------- Empty ---------- */
  .cms-blog__empty { padding: 3.5rem 1rem !important; text-align: center; }
  .cms-blog__empty-icon {
    display: grid;
    width: 3.25rem;
    height: 3.25rem;
    margin: 0 auto .85rem;
    place-items: center;
    border-radius: 1rem;
    background: #f1f5fb;
    color: #8292aa;
    font-size: 1.35rem;
  }

  /* ---------- Pagination ---------- */
  .cms-blog__pagination { padding: .9rem 1.25rem; border-top: 1px solid var(--cb-line); }

  /* ================= Forms ================= */
  .cms-blog-form .form-label { margin-bottom: .4rem; color: #46566e; font-size: .82rem; font-weight: 600; }

  .cms-blog-form .form-control,
  .cms-blog-form .form-select {
    min-height: 2.6rem;
    border-color: #e0e6ef;
    border-radius: .65rem;
    box-shadow: none;
  }

  .cms-blog-form .form-control:focus,
  .cms-blog-form .form-select:focus { border-color: #85a8f3; box-shadow: 0 0 0 .2rem rgba(53, 103, 214, .1); }

  .cms-blog-form .form-text { color: var(--cb-muted); font-size: .78rem; }

  .cms-blog-form .form-check-input { border-color: #cdd8e6; }
  .cms-blog-form .form-check-input:checked { background-color: var(--cb-accent); border-color: var(--cb-accent); }

  /* ---------- Tabs (locales) ---------- */
  .cms-blog-form .nav-tabs { border-bottom: 1px solid var(--cb-line); gap: .25rem; }

  .cms-blog-form .nav-tabs .nav-link {
    border: 0;
    border-bottom: 2px solid transparent;
    border-radius: .5rem .5rem 0 0;
    color: #65748b;
    font-size: .85rem;
    font-weight: 600;
    padding: .6rem 1rem;
    background: transparent;
  }

  .cms-blog-form .nav-tabs .nav-link:hover { color: #315ebd; border-color: transparent; }

  .cms-blog-form .nav-tabs .nav-link.active {
    color: var(--cb-accent);
    border-bottom-color: var(--cb-accent);
    background: transparent;
  }

  .cms-blog-form .tab-content {
    border: 1px solid var(--cb-line) !important;
    border-top: 0 !important;
    border-radius: 0 0 .75rem .75rem;
    padding: 1.25rem !important;
    background: #fff;
  }

  /* ---------- File input ---------- */
  .cms-blog-form input[type="file"] { border-radius: .65rem; }

  .cms-blog-form__preview {
    display: flex;
    align-items: center;
    gap: .65rem;
    margin-top: .55rem;
  }

  .cms-blog-form__preview img { border-radius: .5rem; border: 1px solid var(--cb-line); }

  /* ---------- Responsive ---------- */
  @media (max-width: 991.98px) {
    .cms-blog__filter-form { flex-direction: column; align-items: stretch; }
    .cms-blog__filter-form .form-control,
    .cms-blog__filter-form .form-select { width: 100%; }
  }

  @media (max-width: 767.98px) {
    .cms-blog__hero { align-items: flex-start; flex-direction: column; padding: 1.1rem; }
    .cms-blog__results-head { align-items: flex-start; flex-direction: column; gap: .25rem; padding: .9rem 1rem; }
    .cms-blog__card-body { padding: 1rem; }
  }

  /* ---------- Dark mode ---------- */
  [data-bs-theme="dark"] .cms-blog {
    --cb-ink: #e4ebf6;
    --cb-muted: #93a2b8;
    --cb-line: #29374d;
  }

  [data-bs-theme="dark"] .cms-blog__hero { border-color: #29374d; background: linear-gradient(115deg, #1a2639, #182233 58%, #1b2940); }
  [data-bs-theme="dark"] .cms-blog__title,
  [data-bs-theme="dark"] .cms-blog__card-title,
  [data-bs-theme="dark"] .cms-blog__filters-title,
  [data-bs-theme="dark"] .cms-blog__name { color: #e4ebf6; }

  [data-bs-theme="dark"] .cms-blog__card,
  [data-bs-theme="dark"] .cms-blog__results { border-color: #29374d; background: #182233; }

  [data-bs-theme="dark"] .cms-blog__card-head,
  [data-bs-theme="dark"] .cms-blog__card-foot,
  [data-bs-theme="dark"] .cms-blog__results-head { border-color: #29374d; }
  [data-bs-theme="dark"] .cms-blog__card-foot { background: #141d2c; }

  [data-bs-theme="dark"] .cms-blog__filter-form .form-control,
  [data-bs-theme="dark"] .cms-blog__filter-form .form-select,
  [data-bs-theme="dark"] .cms-blog-form .form-control,
  [data-bs-theme="dark"] .cms-blog-form .form-select { border-color: #334258; background: #111a29; color: #e4ebf6; }

  [data-bs-theme="dark"] .cms-blog__table thead th { border-color: #29374d; background: #202d40; color: #a5b2c5; }
  [data-bs-theme="dark"] .cms-blog__table tbody td { border-color: #29374d; color: #c4cfdf; }
  [data-bs-theme="dark"] .cms-blog__table tbody tr:hover { background: #202d40; }

  [data-bs-theme="dark"] .cms-blog__slug,
  [data-bs-theme="dark"] .cms-blog__empty-icon { background: #202d40; color: #b5c2d5; }

  [data-bs-theme="dark"] .cms-blog__status--success { border-color: #1f5138; background: #14331f; color: #5fd39b; }
  [data-bs-theme="dark"] .cms-blog__status--danger { border-color: #5c2b27; background: #3a1c1a; color: #f0a098; }

  [data-bs-theme="dark"] .cms-blog__btn--subtle,
  [data-bs-theme="dark"] .cms-blog__btn--secondary,
  [data-bs-theme="dark"] .cms-blog__btn--ghost { border-color: #354765; background: #24334d; color: #b6caff; }

  [data-bs-theme="dark"] .cms-blog__btn--danger { border-color: #5c2b27; background: #3a1c1a; color: #f0a098; }

  [data-bs-theme="dark"] .cms-blog-form .form-label { color: #b9c6d8; }
  [data-bs-theme="dark"] .cms-blog-form .nav-tabs { border-color: #29374d; }
  [data-bs-theme="dark"] .cms-blog-form .nav-tabs .nav-link { color: #93a2b8; }
  [data-bs-theme="dark"] .cms-blog-form .nav-tabs .nav-link.active { color: #a9c1ff; border-bottom-color: #a9c1ff; }
  [data-bs-theme="dark"] .cms-blog-form .tab-content { border-color: #29374d !important; background: #182233; }
</style>
