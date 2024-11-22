<div class="dropdown">
    <button class="btn btn-link nav-link position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 300px;">
        <div class="card border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Notifications</h6>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm p-0">Mark all as read</button>
                    </form>
                @endif
            </div>
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                @forelse(auth()->user()->notifications()->latest()->limit(10)->get() as $notification)
                    <div class="notification-item p-3 border-bottom {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">
                        @if($notification->type === 'App\Notifications\StockTransferRequestNotification')
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="mb-1">
                                        <strong>Stock Transfer Request #{{ $notification->data['stock_transfer_id'] }}</strong>
                                    </p>
                                    <p class="mb-1 small text-muted">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    <a href="{{ route('stock_transfers.show', $notification->data['stock_transfer_id']) }}"
                                       class="btn btn-sm btn-primary">View Transfer</a>
                                </div>
                                @unless($notification->read_at)
                                    <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm p-0">
                                            <i class="bi bi-check2-circle"></i>
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        @endif
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                @empty
                    <div class="p-3 text-center text-muted">
                        No notifications
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
