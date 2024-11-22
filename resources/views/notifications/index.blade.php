@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Notifications</h1>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Notifications</h6>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            @forelse($notifications as $notification)
                <div class="notification-item border-bottom p-3 {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">
                    @if($notification->type === 'App\Notifications\StockTransferRequestNotification')
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="mb-1">
                                    <strong>Stock Transfer Request #{{ $notification->data['stock_transfer_id'] }}</strong>
                                </div>
                                <p class="mb-1 text-muted">
                                    {{ $notification->data['message'] }}
                                </p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('stock_transfers.show', $notification->data['stock_transfer_id']) }}"
                                       class="btn btn-sm btn-primary">
                                        View Details
                                    </a>

                                    @unless($notification->read_at)
                                        <form action="{{ route('notifications.mark-as-read', $notification->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endunless

                                    <form action="{{ route('notifications.destroy', $notification->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    No notifications found
                </div>
            @endforelse

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
