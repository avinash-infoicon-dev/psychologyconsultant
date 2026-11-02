<aside class="app-sidebar bg-body-secondary shadow">
  <div class="sidebar-brand">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
      <span class="brand-text fw-light">{{ __('backend.panel') }}</span>
    </a>
  </div>
  <div class="sidebar-wrapper">
    <nav class="mt-2" aria-label="Main navigation">
      {{-- data-accordion="true": opening one submenu closes the others --}}
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="true" role="menu">
        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif">
            <i class="nav-icon bi bi-speedometer2"></i>
            <p>{{ __('backend.dashboard') }}</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.notifications.index') }}" class="nav-link @if(request()->routeIs('admin.notifications.*')) active @endif">
            <i class="nav-icon bi bi-bell"></i>
            <p>
              {{ __('backend.notifications.title') }}
              <span class="nav-badge badge text-bg-danger" data-notification-unread-badge @if (($adminNotificationUnread ?? 0) < 1) hidden @endif>{{ ($adminNotificationUnread ?? 0) > 99 ? '99+' : ($adminNotificationUnread ?? 0) }}</span>
            </p>
          </a>
        </li>
       {{-- @permission('reports.view')
          <li class="nav-item">
            <a href="{{ route('admin.reports.index') }}" class="nav-link @if(request()->routeIs('admin.reports.*')) active @endif">
              <i class="nav-icon bi bi-flag"></i>
              <p>{{ __('backend.menu.reports') }}</p>
            </a>
          </li>
        @endpermission--}}

        @if (auth()->user()->hasAnyRbacRole(['superadmin', 'admin']))
          <li class="nav-item">
            <a href="{{ route('admin.profile.show') }}" class="nav-link @if(request()->routeIs('admin.profile.*')) active @endif">
              <i class="nav-icon bi bi-person"></i>
              <p>{{ __('backend.profile.title') }}</p>
            </a>
          </li>
        @endif

        @php
          $canBilling = auth()->user()->hasPermissionTo('plans.view')
            || auth()->user()->hasPermissionTo('subscriptions.view');
          $billingOpen = request()->routeIs('admin.plans.*')
            || request()->routeIs('admin.subscriptions.*');

          $canCms = auth()->user()->hasPermissionTo('cms.categories.view')
            || auth()->user()->hasPermissionTo('cms.pages.view')
            || auth()->user()->hasPermissionTo('cms.menus.view');
          $cmsOpen = request()->routeIs('admin.cms.*');

          $canAccess = auth()->user()->hasPermissionTo('users.view')
            || auth()->user()->hasPermissionTo('roles.view')
            || auth()->user()->hasPermissionTo('permissions.view')
            || auth()->user()->hasPermissionTo('psychologists.view')
            || auth()->user()->hasPermissionTo('patients.view')
            || auth()->user()->hasPermissionTo('specializations.view')
            || auth()->user()->hasPermissionTo('settings.view')
            || auth()->user()->hasPermissionTo('logs.view');
          $accessOpen = request()->routeIs('admin.users.*')
            || request()->routeIs('admin.roles.*')
            || request()->routeIs('admin.permissions.*')
            || request()->routeIs('admin.psychologists.*')
            || request()->routeIs('admin.patients.*')
            || request()->routeIs('admin.specializations.*')
            || request()->routeIs('admin.settings.*')
            || request()->routeIs('admin.logs.*');
        @endphp

        @if ($canBilling)
          <li class="nav-item @if($billingOpen) menu-open @endif">
            <a href="#" class="nav-link @if($billingOpen) active @endif">
              <i class="nav-icon bi bi-cash-coin"></i>
              <p>
                {{ __('backend.billing') }}
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @permission('plans.view')
                <li class="nav-item">
                  <a href="{{ route('admin.plans.index') }}" class="nav-link @if(request()->routeIs('admin.plans.*')) active @endif">
                    <i class="nav-icon bi bi-tags"></i>
                    <p>{{ __('backend.menu.plans') }}</p>
                  </a>
                </li>
              @endpermission
              @permission('subscriptions.view')
                <li class="nav-item">
                  <a href="{{ route('admin.subscriptions.index') }}" class="nav-link @if(request()->routeIs('admin.subscriptions.*')) active @endif">
                    <i class="nav-icon bi bi-credit-card"></i>
                    <p>{{ __('backend.menu.subscriptions') }}</p>
                  </a>
                </li>
              @endpermission
            </ul>
          </li>
        @endif

       {{--@if ($canCms)
          <li class="nav-item @if($cmsOpen) menu-open @endif">
            <a href="#" class="nav-link @if($cmsOpen) active @endif">
              <i class="nav-icon bi bi-collection"></i>
              <p>
                {{ __('backend.cms.section') }}
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @permission('cms.categories.view')
                <li class="nav-item">
                  <a href="{{ route('admin.cms.categories.index') }}" class="nav-link @if(request()->routeIs('admin.cms.categories.*')) active @endif">
                    <i class="nav-icon bi bi-folder2"></i>
                    <p>{{ __('backend.menu.cms_categories') }}</p>
                  </a>
                </li>
              @endpermission
              @permission('cms.pages.view')
                <li class="nav-item">
                  <a href="{{ route('admin.cms.pages.index') }}" class="nav-link @if(request()->routeIs('admin.cms.pages.*')) active @endif">
                    <i class="nav-icon bi bi-file-earmark-text"></i>
                    <p>{{ __('backend.menu.cms_pages') }}</p>
                  </a>
                </li>
              @endpermission
              @permission('cms.menus.view')
                <li class="nav-item">
                  <a href="{{ route('admin.cms.menus.index') }}" class="nav-link @if(request()->routeIs('admin.cms.menus.*')) active @endif">
                    <i class="nav-icon bi bi-list"></i>
                    <p>{{ __('backend.menu.cms_menus') }}</p>
                  </a>
                </li>
              @endpermission
            </ul>
          </li>
        @endif--}}

        @if ($canAccess)
          <li class="nav-item @if($accessOpen) menu-open @endif">
            <a href="#" class="nav-link @if($accessOpen) active @endif">
              <i class="nav-icon bi bi-shield-lock"></i>
              <p>
                {{ __('backend.rbac') }}
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @permission('users.view')
                <li class="nav-item">
                  <a href="{{ route('admin.users.index') }}" class="nav-link @if(request()->routeIs('admin.users.*')) active @endif">
                    <i class="nav-icon bi bi-people"></i>
                    <p>{{ __('backend.menu.users') }}</p>
                  </a>
                </li>
              @endpermission
              @permission('psychologists.view')
                <li class="nav-item">
                  <a href="{{ route('admin.psychologists.index') }}" class="nav-link @if(request()->routeIs('admin.psychologists.*')) active @endif">
                    <i class="nav-icon bi bi-person-badge"></i>
                    <p>{{ __('backend.menu.psychologists') }}</p>
                  </a>
                </li>
              @endpermission
              @permission('patients.view')
                <li class="nav-item">
                  <a href="{{ route('admin.patients.index') }}" class="nav-link @if(request()->routeIs('admin.patients.*')) active @endif">
                    <i class="nav-icon bi bi-people-fill"></i>
                    <p>{{ __('backend.menu.patients') }}</p>
                  </a>
                </li>
              @endpermission
              @permission('specializations.view')
                <li class="nav-item">
                  <a href="{{ route('admin.specializations.index') }}" class="nav-link @if(request()->routeIs('admin.specializations.*')) active @endif">
                    <i class="nav-icon bi bi-bookmark-star"></i>
                    <p>{{ __('backend.menu.specializations') }}</p>
                  </a>
                </li>
              @endpermission
              @permission('settings.view')
                @php $settingsOpen = request()->routeIs('admin.settings.*'); @endphp
                <li class="nav-item @if($settingsOpen) menu-open @endif">
                  <a href="#" class="nav-link @if($settingsOpen) active @endif">
                    <i class="nav-icon bi bi-gear"></i>
                    <p>
                      {{ __('backend.menu.settings') }}
                      <i class="nav-arrow bi bi-chevron-right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{ route('admin.settings.security') }}" class="nav-link @if(request()->routeIs('admin.settings.security*')) active @endif">
                        <i class="nav-icon bi bi-shield-lock"></i>
                        <p>{{ __('backend.settings.security.title') }}</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route('admin.settings.messaging') }}" class="nav-link @if(request()->routeIs('admin.settings.messaging*')) active @endif">
                        <i class="nav-icon bi bi-chat-dots"></i>
                        <p>{{ __('backend.settings.messaging.title') }}</p>
                      </a>
                    </li>
                    {{--<li class="nav-item">
                      <a href="{{ route('admin.settings.notifications') }}" class="nav-link @if(request()->routeIs('admin.settings.notifications*')) active @endif">
                        <i class="nav-icon bi bi-bell"></i>
                        <p>{{ __('backend.settings.notifications.title') }}</p>
                      </a>
                    </li>--}}
                  </ul>
                </li>
              @endpermission

              {{--@permission('roles.view')
                <li class="nav-item">
                  <a href="{{ route('admin.roles.index') }}" class="nav-link @if(request()->routeIs('admin.roles.*')) active @endif">
                    <i class="nav-icon bi bi-shield-check"></i>
                    <p>{{ __('backend.menu.roles') }}</p>
                  </a>
                </li>
              @endpermission
              @permission('permissions.view')
                <li class="nav-item">
                  <a href="{{ route('admin.permissions.index') }}" class="nav-link @if(request()->routeIs('admin.permissions.*')) active @endif">
                    <i class="nav-icon bi bi-key"></i>
                    <p>{{ __('backend.menu.permissions') }}</p>
                  </a>
                </li>
              @endpermission--}}
              @permission('logs.view')
                {{--<li class="nav-item">
                  <a href="{{ route('admin.logs.activity') }}" class="nav-link @if(request()->routeIs('admin.logs.activity')) active @endif">
                    <i class="nav-icon bi bi-journal-text"></i>
                    <p>{{ __('backend.menu.activity_logs') }}</p>
                  </a>
                </li>--}}
                <li class="nav-item">
                  <a href="{{ route('admin.logs.audit') }}" class="nav-link @if(request()->routeIs('admin.logs.audit')) active @endif">
                    <i class="nav-icon bi bi-clipboard-data"></i>
                    <p>{{ __('backend.menu.audit_logs') }}</p>
                  </a>
                </li>
              @endpermission
            </ul>
          </li>
        @endif

        {{--@role(['superadmin', 'admin'])
          <li class="nav-item">
            <a href="{{ url('/docs/api') }}" class="nav-link" target="_blank" rel="noopener">
              <i class="nav-icon bi bi-file-earmark-code"></i>
              <p>{{ __('backend.menu.api_docs') }}</p>
            </a>
          </li>
        @endrole--}}
      </ul>
    </nav>
  </div>
</aside>
