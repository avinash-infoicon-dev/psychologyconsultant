@push('styles')
  <style>
    .admin-dashboard {
      --dashboard-ink: #17243b;
      --dashboard-muted: #748198;
      --dashboard-border: #e7ecf3;
    }

    .admin-dashboard__hero {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
      padding: 1.35rem 1.55rem;
      border: 1px solid #dfe8fb;
      border-radius: 1rem;
      background: linear-gradient(115deg, #f1f6ff, #fff 64%, #f5f8ff);
      box-shadow: 0 8px 26px rgba(34, 65, 120, .055);
    }

    .admin-dashboard__hero-icon {
      display: grid;
      width: 3.2rem;
      height: 3.2rem;
      flex: 0 0 3.2rem;
      place-items: center;
      border-radius: 1rem;
      background: linear-gradient(145deg, #477be7, #2855bd);
      box-shadow: 0 7px 16px rgba(53, 103, 214, .2);
      color: #fff;
      font-size: 1.3rem;
    }

    .admin-dashboard__welcome {
      margin: 0 0 .2rem;
      color: var(--dashboard-ink);
      font-size: 1.15rem;
      font-weight: 700;
    }

    .admin-dashboard__subtitle {
      margin: 0;
      color: var(--dashboard-muted);
      font-size: .88rem;
    }

    .admin-dashboard__section {
      margin-bottom: 1.5rem;
    }

    .admin-dashboard__section-heading {
      display: flex;
      align-items: center;
      gap: .6rem;
      margin: 0 0 .85rem;
      color: var(--dashboard-ink);
      font-size: 1rem;
      font-weight: 700;
    }

    .admin-dashboard__section-heading i {
      color: #4772ce;
      font-size: 1.05rem;
    }

    .admin-dashboard__metric {
      position: relative;
      display: flex;
      min-height: 132px;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
      padding: 1rem 1.05rem .85rem;
      border: 1px solid var(--dashboard-border);
      border-radius: .9rem;
      background: #fff;
      box-shadow: 0 5px 18px rgba(24, 39, 75, .045);
      transition: transform .16s ease, box-shadow .16s ease;
    }

    .admin-dashboard__metric:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 24px rgba(24, 39, 75, .09);
    }

    .admin-dashboard__metric-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: .75rem;
    }

    .admin-dashboard__metric-label {
      display: block;
      color: var(--dashboard-muted);
      font-size: .8rem;
      font-weight: 600;
      line-height: 1.35;
    }

    .admin-dashboard__metric-value {
      display: block;
      margin-top: .35rem;
      color: var(--dashboard-ink);
      font-size: 1.8rem;
      font-weight: 750;
      letter-spacing: -.04em;
      line-height: 1.1;
    }

    .admin-dashboard__metric-icon {
      display: grid;
      width: 2.45rem;
      height: 2.45rem;
      flex: 0 0 2.45rem;
      place-items: center;
      border-radius: .75rem;
      background: var(--metric-soft, #edf3ff);
      color: var(--metric-color, #416bc7);
      font-size: 1.1rem;
    }

    .admin-dashboard__metric-note {
      margin-top: .85rem;
      padding-top: .65rem;
      border-top: 1px solid #eff2f7;
      color: #8793a6;
      font-size: .75rem;
      line-height: 1.45;
    }

    .admin-dashboard__metric-note strong {
      color: #596980;
      font-weight: 700;
    }

    .admin-dashboard__metric--blue { --metric-color: #416bc7; --metric-soft: #edf3ff; }
    .admin-dashboard__metric--green { --metric-color: #18866b; --metric-soft: #e8f7f1; }
    .admin-dashboard__metric--cyan { --metric-color: #14839a; --metric-soft: #e7f7fa; }
    .admin-dashboard__metric--slate { --metric-color: #64748b; --metric-soft: #eef1f5; }
    .admin-dashboard__metric--amber { --metric-color: #b7791f; --metric-soft: #fff6e6; }
    .admin-dashboard__metric--violet { --metric-color: #7353bb; --metric-soft: #f2edfc; }

    [data-bs-theme="dark"] .admin-dashboard__hero {
      border-color: #29374d;
      background: linear-gradient(115deg, #1a2639, #182233 64%, #1b2940);
    }

    [data-bs-theme="dark"] .admin-dashboard__welcome,
    [data-bs-theme="dark"] .admin-dashboard__section-heading,
    [data-bs-theme="dark"] .admin-dashboard__metric-value {
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .admin-dashboard__metric {
      border-color: #29374d;
      background: #182233;
    }

    [data-bs-theme="dark"] .admin-dashboard__metric-note {
      border-color: #29374d;
      color: #9cabc0;
    }

    [data-bs-theme="dark"] .admin-dashboard__metric-note strong {
      color: #d1dbea;
    }

    @media (max-width: 575.98px) {
      .admin-dashboard__hero {
        padding: 1rem;
      }

      .admin-dashboard__metric {
        min-height: 120px;
        padding: .8rem;
      }

      .admin-dashboard__metric-value {
        font-size: 1.55rem;
      }
    }
  </style>
@endpush

<section class="admin-dashboard__section" aria-labelledby="dashboard-users-heading">
  <h2 class="admin-dashboard__section-heading" id="dashboard-users-heading">
    <i class="bi bi-people" aria-hidden="true"></i>{{ __('backend.dashboard_pages.section_users') }}
  </h2>
  <div class="row g-3">
    <div class="col-lg-6 col-md-6">
      <article class="admin-dashboard__metric admin-dashboard__metric--blue">
        <div class="admin-dashboard__metric-top">
          <div>
            <span class="admin-dashboard__metric-label">{{ __('backend.dashboard_pages.patients_total') }}</span>
            <strong class="admin-dashboard__metric-value">{{ number_format($stats['patients']['total']) }}</strong>
          </div>
          <span class="admin-dashboard__metric-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
        </div>
        <div class="admin-dashboard__metric-note">
          {{ __('backend.dashboard_pages.patients_new_month') }}:
          <strong>{{ number_format($stats['patients']['new_this_month']) }}</strong>
        </div>
      </article>
    </div>
    <div class="col-lg-6 col-md-6">
      <article class="admin-dashboard__metric admin-dashboard__metric--green">
        <div class="admin-dashboard__metric-top">
          <div>
            <span class="admin-dashboard__metric-label">{{ __('backend.dashboard_pages.psychologists_active') }}</span>
            <strong class="admin-dashboard__metric-value">{{ number_format($stats['psychologists']['active'] ?? $stats['psychologists']['verified']) }}</strong>
          </div>
          <span class="admin-dashboard__metric-icon"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
        </div>
        <div class="admin-dashboard__metric-note">
          {{ __('backend.dashboard_pages.psychologists_awaiting') }}:
          <strong>{{ number_format($stats['psychologists']['pending_verification']) }}</strong>
          <span aria-hidden="true"> · </span>
          {{ __('backend.dashboard_pages.psychologists_total') }}:
          <strong>{{ number_format($stats['psychologists']['total']) }}</strong>
        </div>
      </article>
    </div>
  </div>
</section>

<section class="admin-dashboard__section" aria-labelledby="dashboard-subscriptions-heading">
  <h2 class="admin-dashboard__section-heading" id="dashboard-subscriptions-heading">
    <i class="bi bi-credit-card-2-front" aria-hidden="true"></i>{{ __('backend.dashboard_pages.section_subscriptions') }}
  </h2>
  <div class="row g-3">
    <div class="col-lg-6 col-md-6">
      <article class="admin-dashboard__metric admin-dashboard__metric--cyan">
        <div class="admin-dashboard__metric-top">
          <div>
            <span class="admin-dashboard__metric-label">{{ __('backend.dashboard_pages.subscriptions_active') }}</span>
            <strong class="admin-dashboard__metric-value">{{ number_format($stats['subscriptions']['active']) }}</strong>
          </div>
          <span class="admin-dashboard__metric-icon"><i class="bi bi-card-checklist" aria-hidden="true"></i></span>
        </div>
        <div class="admin-dashboard__metric-note">
          {{ __('backend.dashboard_pages.subscriptions_new_month') }}:
          <strong>{{ number_format($stats['subscriptions']['new_this_month'] ?? 0) }}</strong>
        </div>
      </article>
    </div>
    <div class="col-lg-6 col-md-6">
      <article class="admin-dashboard__metric admin-dashboard__metric--slate">
        <div class="admin-dashboard__metric-top">
          <div>
            <span class="admin-dashboard__metric-label">{{ __('backend.dashboard_pages.subscriptions_ended') }}</span>
            <strong class="admin-dashboard__metric-value">{{ number_format(($stats['subscriptions']['expired'] ?? 0) + ($stats['subscriptions']['cancelled'] ?? 0)) }}</strong>
          </div>
          <span class="admin-dashboard__metric-icon"><i class="bi bi-x-circle" aria-hidden="true"></i></span>
        </div>
        <div class="admin-dashboard__metric-note">
          {{ __('backend.dashboard_pages.subscriptions_expired') }}:
          <strong>{{ number_format($stats['subscriptions']['expired'] ?? 0) }}</strong>
          <span aria-hidden="true"> · </span>
          {{ __('backend.dashboard_pages.subscriptions_cancelled') }}:
          <strong>{{ number_format($stats['subscriptions']['cancelled'] ?? 0) }}</strong>
        </div>
      </article>
    </div>
  </div>
</section>

<section class="admin-dashboard__section" aria-labelledby="dashboard-activity-heading">
  <h2 class="admin-dashboard__section-heading" id="dashboard-activity-heading">
    <i class="bi bi-graph-up-arrow" aria-hidden="true"></i>{{ __('backend.dashboard_pages.section_activity') }}
  </h2>
  <div class="row g-3">
    <div class="col-xl-3 col-sm-6">
      <article class="admin-dashboard__metric admin-dashboard__metric--blue">
        <div class="admin-dashboard__metric-top">
          <div>
            <span class="admin-dashboard__metric-label">{{ __('backend.dashboard_pages.conversations_active') }}</span>
            <strong class="admin-dashboard__metric-value">{{ number_format($stats['activity']['active_conversations'] ?? 0) }}</strong>
          </div>
          <span class="admin-dashboard__metric-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></span>
        </div>
      </article>
    </div>
    <div class="col-xl-3 col-sm-6">
      <article class="admin-dashboard__metric admin-dashboard__metric--amber">
        <div class="admin-dashboard__metric-top">
          <div>
            <span class="admin-dashboard__metric-label">{{ __('backend.dashboard_pages.relationship_requests') }}</span>
            <strong class="admin-dashboard__metric-value">{{ number_format($stats['activity']['new_requests'] ?? 0) }}</strong>
          </div>
          <span class="admin-dashboard__metric-icon"><i class="bi bi-person-plus" aria-hidden="true"></i></span>
        </div>
        <div class="admin-dashboard__metric-note">
          {{ __('backend.dashboard_pages.relationship_requests_pending') }}:
          <strong>{{ number_format($stats['activity']['pending_requests'] ?? 0) }}</strong>
        </div>
      </article>
    </div>
    <div class="col-xl-3 col-sm-6">
      <article class="admin-dashboard__metric admin-dashboard__metric--green">
        <div class="admin-dashboard__metric-top">
          <div>
            <span class="admin-dashboard__metric-label">{{ __('backend.dashboard_pages.exchanges_consumed') }}</span>
            <strong class="admin-dashboard__metric-value">{{ number_format($stats['activity']['exchanges_consumed'] ?? 0) }}</strong>
          </div>
          <span class="admin-dashboard__metric-icon"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></span>
        </div>
        <div class="admin-dashboard__metric-note">
          {{ __('backend.dashboard_pages.exchanges_consumed_month') }}:
          <strong>{{ number_format($stats['activity']['exchanges_consumed_month'] ?? 0) }}</strong>
        </div>
      </article>
    </div>
    <div class="col-xl-3 col-sm-6">
      <article class="admin-dashboard__metric admin-dashboard__metric--violet">
        <div class="admin-dashboard__metric-top">
          <div>
            <span class="admin-dashboard__metric-label">{{ __('backend.dashboard_pages.messages_today') }}</span>
            <strong class="admin-dashboard__metric-value">{{ number_format($stats['messages']['sent_today']) }}</strong>
          </div>
          <span class="admin-dashboard__metric-icon"><i class="bi bi-chat-text" aria-hidden="true"></i></span>
        </div>
      </article>
    </div>
  </div>
</section>
