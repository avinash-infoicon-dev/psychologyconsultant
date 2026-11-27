{{-- Shared modern styling for admin screens. Pushed onto the `styles` stack. --}}
<style>
  .adm {
    --adm-accent: #3567d6;
    --adm-ink: #17243b;
    --adm-muted: #68778d;
    --adm-line: #e8edf4;
  }

  /* ---------- Hero ---------- */
  .adm__hero {
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

  .adm__hero::after {
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

  .adm__heading { display: flex; align-items: center; gap: 1rem; min-width: 0; }

  .adm__hero-icon {
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

  .adm__title { margin: 0; color: var(--adm-ink); font-size: 1.25rem; font-weight: 700; letter-spacing: -.02em; }
  .adm__subtitle { margin: .2rem 0 0; color: var(--adm-muted); font-size: .88rem; }

  .adm__actions { position: relative; z-index: 1; display: flex; flex-wrap: wrap; gap: .5rem; }

  /* ---------- Buttons ---------- */
  .adm__btn {
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

  .adm__btn--primary { background: var(--adm-accent); color: #fff; box-shadow: 0 4px 10px rgba(53, 103, 214, .18); }
  .adm__btn--primary:hover { background: #2855bd; color: #fff; }

  .adm__btn--ghost { border-color: #dce6fa; background: rgba(255, 255, 255, .86); color: #315ebd; }
  .adm__btn--ghost:hover { background: #eaf0fe; color: #2855bd; }

  .adm__btn--subtle { border-color: var(--adm-line); background: #fff; color: #5f6e82; }
  .adm__btn--subtle:hover { background: #f4f7ff; color: #315ebd; }

  .adm__btn--danger { border-color: #f3cdca; background: #fff; color: #b42318; }
  .adm__btn--danger:hover { background: #fdeceb; color: #9a1f17; }

  .adm__btn--warn { border-color: #ecd9a8; background: #fff; color: #8a6410; }
  .adm__btn--warn:hover { background: #fbf3df; color: #75530d; }

  .adm__btn--success { border-color: #bce8d2; background: #fff; color: #16794c; }
  .adm__btn--success:hover { background: #e8f6ef; color: #136c43; }

  .adm__btn--secondary { border-color: var(--adm-line); background: #f4f6fa; color: #46566e; }
  .adm__btn--secondary:hover { background: #e9edf4; color: #28364d; }

  /* ---------- Card ---------- */
  .adm__card { overflow: hidden; border: 1px solid var(--adm-line); border-radius: 1rem; background: #fff; box-shadow: 0 6px 24px rgba(24, 39, 75, .045); }

  .adm__card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.05rem 1.25rem;
    border-bottom: 1px solid var(--adm-line);
  }

  .adm__card-title { display: flex; align-items: center; gap: .55rem; margin: 0; color: var(--adm-ink); font-size: .98rem; font-weight: 700; }
  .adm__card-title i { color: var(--adm-accent); }

  .adm__card-body { padding: 1.25rem; }

  .adm__card-foot { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: .5rem; padding: 1rem 1.25rem; border-top: 1px solid var(--adm-line); background: #fafbfd; }

  .adm__section { margin-top: 1.15rem; }

  /* ---------- Filters ---------- */
  .adm__filters { margin-top: 1.15rem; padding: 1.1rem 1.25rem 1.2rem; }

  .adm__filters-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: .85rem; }

  .adm__filters-title { display: flex; align-items: center; gap: .55rem; margin: 0; color: var(--adm-ink); font-size: .95rem; font-weight: 650; }
  .adm__filters-title i { color: var(--adm-accent); }

  .adm__clear { display: inline-flex; align-items: center; gap: .35rem; color: #65748b; font-size: .825rem; font-weight: 600; text-decoration: none; }
  .adm__clear:hover { color: var(--adm-accent); }

  .adm__filter-form { display: flex; flex-wrap: wrap; gap: .65rem; align-items: center; }

  .adm__filter-form .form-control,
  .adm__filter-form .form-select { min-height: 2.6rem; border-color: #e0e6ef; border-radius: .65rem; box-shadow: none; }

  .adm__filter-form .form-control { min-width: 200px; flex: 1 1 220px; }

  .adm__filter-form .form-control:focus,
  .adm__filter-form .form-select:focus { border-color: #85a8f3; box-shadow: 0 0 0 .2rem rgba(53, 103, 214, .1); }

  /* ---------- Results ---------- */
  .adm__results { margin-top: 1.15rem; }

  .adm__results-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.25rem; border-bottom: 1px solid var(--adm-line); }

  .adm__range { color: var(--adm-muted); font-size: .82rem; }

  .adm__table-wrap { overflow-x: auto; }

  .adm__table { margin: 0; vertical-align: middle; }

  .adm__table thead th {
    padding: .8rem 1rem;
    border-bottom: 1px solid var(--adm-line);
    background: #f8faff;
    color: #77859a;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .adm__table tbody td { padding: .9rem 1rem; border-color: #eef1f6; color: #344258; font-size: .84rem; }
  .adm__table tbody tr { transition: background-color .15s ease; }
  .adm__table tbody tr:hover { background: #f8faff; }

  .adm__num { font-variant-numeric: tabular-nums; }

  .adm__name { display: block; color: #28364d; font-weight: 650; }
  .adm__sub { display: block; margin-top: .15rem; color: #8a97aa; font-size: .75rem; }

  .adm__slug {
    display: inline-block;
    padding: .12rem .4rem;
    border-radius: .35rem;
    background: #f3f5f9;
    color: #7a8699;
    font-size: .72rem;
  }

  .adm__muted { color: #a3adbd; }

  .adm__row-actions { display: flex; flex-wrap: wrap; gap: .4rem; white-space: nowrap; }

  .adm__thumb { width: 72px; height: 48px; border-radius: .5rem; object-fit: cover; border: 1px solid var(--adm-line); background: #f3f5f9; }

  /* ---------- Person cell ---------- */
  .adm__person { display: flex; align-items: center; gap: .65rem; min-width: 150px; }

  .adm__avatar {
    display: grid;
    width: 2.1rem;
    height: 2.1rem;
    flex: 0 0 2.1rem;
    place-items: center;
    border-radius: 50%;
    background: #edf2ff;
    color: #4166b4;
    font-size: .7rem;
    font-weight: 700;
  }

  /* ---------- Status pill ---------- */
  .adm__status {
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

  .adm__status::before { width: .45rem; height: .45rem; border-radius: 50%; background: currentColor; content: ""; }
  .adm__status--success { border-color: #bce8d2; background: #e8f6ef; color: #16794c; }
  .adm__status--danger { border-color: #f3cdca; background: #fdeceb; color: #b42318; }
  .adm__status--warning { border-color: #ecd9a8; background: #fbf3df; color: #8a6410; }
  .adm__status--secondary { border-color: #e1e9fa; background: #f4f7ff; color: #5f6e82; }
  .adm__status--dark { border-color: #d3dcea; background: #e9edf4; color: #344258; }

  /* ---------- Role chip ---------- */
  .adm__chip { display: inline-flex; align-items: center; padding: .22rem .5rem; margin: 0 .25rem .25rem 0; border: 1px solid #e1e9fa; border-radius: .4rem; background: #f4f7ff; color: #3e5f9e; font-size: .72rem; font-weight: 600; white-space: nowrap; }

  /* ---------- Empty ---------- */
  .adm__empty { padding: 3.5rem 1rem !important; text-align: center; }
  .adm__empty-icon { display: grid; width: 3.25rem; height: 3.25rem; margin: 0 auto .85rem; place-items: center; border-radius: 1rem; background: #f1f5fb; color: #8292aa; font-size: 1.35rem; }
  .adm__empty-title { display: block; color: var(--adm-ink); font-size: .95rem; font-weight: 650; }
  .adm__empty-hint { display: block; margin-top: .25rem; color: var(--adm-muted); font-size: .82rem; }

  /* ---------- Pagination ---------- */
  .adm__pagination { padding: .9rem 1.25rem; border-top: 1px solid var(--adm-line); }

  /* ================= Settings forms ================= */
  .adm-form .form-label { margin-bottom: .4rem; color: #46566e; font-size: .82rem; font-weight: 600; }

  .adm-form .form-control,
  .adm-form .form-select { min-height: 2.6rem; border-color: #e0e6ef; border-radius: .65rem; box-shadow: none; }

  .adm-form .form-control:focus,
  .adm-form .form-select:focus { border-color: #85a8f3; box-shadow: 0 0 0 .2rem rgba(53, 103, 214, .1); }

  .adm-form .form-text { color: var(--adm-muted); font-size: .78rem; }
  .adm-form .form-check-input { border-color: #cdd8e6; }
  .adm-form .form-check-input:checked { background-color: var(--adm-accent); border-color: var(--adm-accent); }

  .adm-form .form-switch .form-check-input { width: 2.6rem; height: 1.35rem; }

  .adm__section-heading { display: flex; align-items: center; gap: .55rem; margin: 0 0 .85rem; color: var(--adm-ink); font-size: 1rem; font-weight: 700; }
  .adm__section-heading i { color: var(--adm-accent); }
  .adm__divider { border-color: var(--adm-line); margin: 1.5rem 0; opacity: 1; }

  .adm__checks { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: .55rem; }

  .adm__check {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .7rem .85rem;
    border: 1px solid var(--adm-line);
    border-radius: .65rem;
    background: #fff;
    margin: 0;
  }

  .adm__check .form-check-input { margin: 0; flex: 0 0 auto; }

  /* ---------- Responsive ---------- */
  @media (max-width: 991.98px) {
    .adm__filter-form { flex-direction: column; align-items: stretch; }
    .adm__filter-form .form-control,
    .adm__filter-form .form-select { width: 100%; }
  }

  @media (max-width: 767.98px) {
    .adm__hero { align-items: flex-start; flex-direction: column; padding: 1.1rem; }
    .adm__results-head { align-items: flex-start; flex-direction: column; gap: .25rem; padding: .9rem 1rem; }
    .adm__card-body { padding: 1rem; }
  }

  /* ---------- Dark mode ---------- */
  [data-bs-theme="dark"] .adm { --adm-ink: #e4ebf6; --adm-muted: #93a2b8; --adm-line: #29374d; }

  [data-bs-theme="dark"] .adm__hero { border-color: #29374d; background: linear-gradient(115deg, #1a2639, #182233 58%, #1b2940); }

  [data-bs-theme="dark"] .adm__title,
  [data-bs-theme="dark"] .adm__card-title,
  [data-bs-theme="dark"] .adm__filters-title,
  [data-bs-theme="dark"] .adm__name,
  [data-bs-theme="dark"] .adm__section-heading,
  [data-bs-theme="dark"] .adm__empty-title { color: #e4ebf6; }

  [data-bs-theme="dark"] .adm__card,
  [data-bs-theme="dark"] .adm__results,
  [data-bs-theme="dark"] .adm__check { border-color: #29374d; background: #182233; }

  [data-bs-theme="dark"] .adm__card-head,
  [data-bs-theme="dark"] .adm__card-foot,
  [data-bs-theme="dark"] .adm__results-head { border-color: #29374d; }
  [data-bs-theme="dark"] .adm__card-foot { background: #141d2c; }

  [data-bs-theme="dark"] .adm__filter-form .form-control,
  [data-bs-theme="dark"] .adm__filter-form .form-select,
  [data-bs-theme="dark"] .adm-form .form-control,
  [data-bs-theme="dark"] .adm-form .form-select { border-color: #334258; background: #111a29; color: #e4ebf6; }

  [data-bs-theme="dark"] .adm__table thead th { border-color: #29374d; background: #202d40; color: #a5b2c5; }
  [data-bs-theme="dark"] .adm__table tbody td { border-color: #29374d; color: #c4cfdf; }
  [data-bs-theme="dark"] .adm__table tbody tr:hover { background: #202d40; }

  [data-bs-theme="dark"] .adm__slug,
  [data-bs-theme="dark"] .adm__empty-icon { background: #202d40; color: #b5c2d5; }

  [data-bs-theme="dark"] .adm__avatar { background: #24334d; color: #b6caff; }

  [data-bs-theme="dark"] .adm__status--success { border-color: #1f5138; background: #14331f; color: #5fd39b; }
  [data-bs-theme="dark"] .adm__status--danger { border-color: #5c2b27; background: #3a1c1a; color: #f0a098; }
  [data-bs-theme="dark"] .adm__status--warning { border-color: #5a4a1e; background: #382f16; color: #e3c46a; }
  [data-bs-theme="dark"] .adm__status--secondary,
  [data-bs-theme="dark"] .adm__status--dark,
  [data-bs-theme="dark"] .adm__chip { border-color: #354765; background: #24334d; color: #b6caff; }

  [data-bs-theme="dark"] .adm__btn--subtle,
  [data-bs-theme="dark"] .adm__btn--secondary,
  [data-bs-theme="dark"] .adm__btn--ghost { border-color: #354765; background: #24334d; color: #b6caff; }
  [data-bs-theme="dark"] .adm__btn--danger { border-color: #5c2b27; background: #3a1c1a; color: #f0a098; }
  [data-bs-theme="dark"] .adm__btn--warn { border-color: #5a4a1e; background: #382f16; color: #e3c46a; }
  [data-bs-theme="dark"] .adm__btn--success { border-color: #1f5138; background: #14331f; color: #5fd39b; }

  [data-bs-theme="dark"] .adm-form .form-label { color: #b9c6d8; }
  [data-bs-theme="dark"] .adm__divider { border-color: #29374d; }
</style>
