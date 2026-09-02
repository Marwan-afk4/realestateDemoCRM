<nav class="navbar navbar-vertical navbar-expand-lg" style="display:none;">
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                <li class="nav-item">
                    {{-- Section: Dashboard --}}
                    <p class="navbar-vertical-label">{{ __('Home') }}</p>
                    <hr class="navbar-vertical-line" />

                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'home' ? 'active' : '' }}" href="{{ route('home') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="home"></span></span>
                                <span class="nav-link-text">{{ __('Home') }}</span>
                            </div>
                        </a>
                    </div>
                    @can('view-home-names')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'home-names' ? 'active' : '' }}" href="{{ route('home-names.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span class="fa fa-home"></span></span>
                                <span class="nav-link-text">{{ __('Home Names') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan

                    {{-- Section: Users --}}
                    @canany(['view-users','view-brockers'])
                    <p class="navbar-vertical-label mt-3">{{ __('Users & CRM') }}</p>
                    <hr class="navbar-vertical-line" />

                    @can('view-users')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'users' ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="users"></span></span>
                                <span class="nav-link-text">{{ __('Users') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-brockers')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'brockers' ? 'active' : '' }}" href="{{ route('brockers.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="briefcase"></span></span>
                                <span class="nav-link-text">{{ __('Developer Sales') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @endcanany

                    {{-- Section: Real Estate --}}
                    @canany(['view-uptowns','view-uptown-types','view-unit-sub-types','view-developers','view-deals'])
                    <p class="navbar-vertical-label mt-3">{{ __('Real Estate') }}</p>
                    <hr class="navbar-vertical-line" />

                    @can('view-uptowns')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'uptowns' ? 'active' : '' }}" href="{{ route('uptowns.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="map-pin"></span></span>
                                <span class="nav-link-text">{{ __('Units') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-uptown-types')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'uptown-types' ? 'active' : '' }}" href="{{ route('uptown-types.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="layers"></span></span>
                                <span class="nav-link-text">{{ __('Units Types') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-unit-sub-types')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'unit-sub-types' ? 'active' : '' }}" href="{{ route('unit-sub-types.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="tag"></span></span>
                                <span class="nav-link-text">{{ __('Unit Sub-Types') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-developers')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'developers' ? 'active' : '' }}" href="{{ route('developers.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="database"></span></span>
                                <span class="nav-link-text">{{ __('Developers') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-deals')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'deals' ? 'active' : '' }}" href="{{ route('deals.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="tag"></span></span>
                                <span class="nav-link-text">{{ __('Deals') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @endcanany

                    {{-- Section: Requests --}}
                    @canany(['view-sell-requests','view-apartment-installments','view-requests'])
                    <p class="navbar-vertical-label mt-3">{{ __('User Requests') }}</p>
                    <hr class="navbar-vertical-line" />

                    @can('view-sell-requests')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'sell-requests' ? 'active' : '' }}" href="{{ route('sell-requests.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="send"></span></span>
                                <span class="nav-link-text">{{ __('Units Requests') }}</span>
                                @php $sellCount = \App\Models\SellRequest::where('status','pending')->count(); @endphp
                                @if ($sellCount > 0) <span class="badge bg-danger ms-2">{{ $sellCount }}</span> @endif
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-apartment-installments')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'apartment-installments' ? 'active' : '' }}" href="{{ route('apartment-installments.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="clock"></span></span>
                                <span class="nav-link-text">{{ __('Mortgage Requests') }}</span>
                                @php $instCount = \App\Models\BuyAppartmentInstallment::where('status','pending')->count(); @endphp
                                @if ($instCount > 0) <span class="badge bg-danger ms-2">{{ $instCount }}</span> @endif
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-requests')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'requests' ? 'active' : '' }}" href="{{ route('requests.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="inbox"></span></span>
                                <span class="nav-link-text">{{ __('Complaints') }}</span>
                                @php $pendingCount = \App\Models\Complaint::where('status', 'open')->count(); @endphp
                                @if ($pendingCount > 0) <span class="badge bg-danger ms-2">{{ $pendingCount }}</span> @endif
                            </div>
                        </a>
                    </div>
                    @endcan
                    @endcanany

                    {{-- Section: Content --}}
                    @canany(['view-ads','view-contracts','view-contract-agreements','view-bot-messages','view-push-notifications'])
                    <p class="navbar-vertical-label mt-3">{{ __('Marketing & Content') }}</p>
                    <hr class="navbar-vertical-line" />

                    @can('view-ads')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'ads' ? 'active' : '' }}" href="{{ route('ads.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="image"></span></span>
                                <span class="nav-link-text">{{ __('Ads') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-contracts')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'contracts' ? 'active' : '' }}" href="{{ route('contracts.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="file-text"></span></span>
                                <span class="nav-link-text">{{ __('Contracts') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-policies')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'policies' ? 'active' : '' }}" href="{{ route('policies.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="file-text"></span></span>
                                <span class="nav-link-text">{{ __('Policy Terms') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-contract-agreements')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'contract-agreements' ? 'active' : '' }}" href="{{ route('contract-agreements.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="check-square"></span></span>
                                <span class="nav-link-text">{{ __('Contract Agreements') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-bot-messages')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'bot-messages' ? 'active' : '' }}" href="{{ route('bot-messages.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="message-square"></span></span>
                                <span class="nav-link-text">{{ __('Bot Messages') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-push-notifications')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'push-notifications' ? 'active' : '' }}" href="{{ route('push-notifications.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="bell"></span></span>
                                <span class="nav-link-text">{{ __('Push Notifications') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @endcanany

                    {{-- Section: Admin Management --}}
                    @canany(['view-admins','view-roles'])
                    <p class="navbar-vertical-label mt-3">{{ __('Admin Management') }}</p>
                    <hr class="navbar-vertical-line" />

                    @can('view-roles')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'roles' ? 'active' : '' }}" href="{{ route('admin-roles.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="shield"></span></span>
                                <span class="nav-link-text">{{ __('Roles') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @can('view-admins')
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ isset($currentPage) && $currentPage == 'admins' ? 'active' : '' }}" href="{{ route('admin-users.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="user-check"></span></span>
                                <span class="nav-link-text">{{ __('Admins') }}</span>
                            </div>
                        </a>
                    </div>
                    @endcan
                    @endcanany

                </li>
            </ul>
        </div>
    </div>
    <div class="navbar-vertical-footer">
        <button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center">
            <span class="uil uil-left-arrow-to-left fs-8"></span>
            <span class="uil uil-arrow-from-right fs-8"></span>
            <span class="navbar-vertical-footer-text ms-2">Collapsed View</span>
        </button>
    </div>
</nav>
