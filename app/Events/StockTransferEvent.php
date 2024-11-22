<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\StockTransfer;

class StockTransferEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stockTransfer;
    public $action;

    public function __construct(StockTransfer $stockTransfer, $action)
    {
        $this->stockTransfer = $stockTransfer;
        $this->action = $action;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('stock-transfers');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->stockTransfer->id,
            'status' => $this->stockTransfer->status,
            'action' => $this->action,
            'product' => $this->stockTransfer->inventory->product->name,
            'quantity' => $this->stockTransfer->quantity,
            'from_branch' => $this->stockTransfer->fromBranch->name,
            'to_branch' => $this->stockTransfer->toBranch->name,
            'updated_at' => $this->stockTransfer->updated_at->toDateTimeString(),
        ];
    }
}
