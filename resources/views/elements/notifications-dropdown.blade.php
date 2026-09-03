@php
    $navbarNotifications = auth()->user()?->notifications()->limit(15)->get() ?? collect();
    $unreadCount = auth()->user()?->unreadNotifications()->count() ?? 0;
@endphp
<div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret"
    id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotification">
    <div class="card position-relative border-0">
        <div class="card-header p-2">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="text-body-emphasis mb-0">{{ __('Notifications') }}</h5>
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button class="btn btn-link p-0 fs-9 fw-normal" type="submit">{{ __('Mark all as read') }}</button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="scrollbar-overlay" style="max-height: 27rem;">
                @forelse ($navbarNotifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = $notification->read_at === null;
                        $href = isset($data['contact_id']) ? route('contacts.show', $data['contact_id']) : '#';
                    @endphp
                    <a href="{{ $href }}"
                        class="d-block px-2 px-sm-3 py-3 notification-card position-relative text-decoration-none text-body {{ $isUnread ? 'unread' : 'read' }} {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-start gap-3">
                            <div class="avatar avatar-m">
                                <div class="avatar-name rounded-circle"><span data-feather="bell"></span></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="fw-semibold fs-9 text-truncate">{{ $data['title'] ?? __('CRM reminder') }}</div>
                                <p class="fs-9 text-body-secondary mb-1">
                                    @if(!empty($data['contact_name']))
                                        {{ $data['contact_name'] }} ·
                                    @endif
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                                @if(!empty($data['due_at']))
                                    <p class="fs-10 text-body-tertiary mb-0">{{ __('Due') }} {{ \Carbon\Carbon::parse($data['due_at'])->format('M j, H:i') }}</p>
                                @endif
                            </div>
                            @if($isUnread)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" onclick="event.stopPropagation();">
                                    @csrf
                                    <button class="btn btn-sm btn-phoenix-secondary" type="submit">{{ __('Read') }}</button>
                                </form>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="px-3 py-5 text-center text-body-tertiary fs-9">{{ __('No notifications yet.') }}</div>
                @endforelse
            </div>
        </div>
        @if($unreadCount > 0)
            <div class="card-footer p-2 border-top border-translucent text-center fs-10 text-body-tertiary">
                {{ trans_choice(':count unread notification|:count unread notifications', $unreadCount, ['count' => $unreadCount]) }}
            </div>
        @endif
    </div>
</div>
