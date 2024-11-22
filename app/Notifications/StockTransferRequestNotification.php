<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\StockTransfer;

class StockTransferRequestNotification extends Notification
{
    use Queueable;

    protected $stockTransfer;

    public function __construct(StockTransfer $stockTransfer)
    {
        $this->stockTransfer = $stockTransfer;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'stock_transfer_id' => $this->stockTransfer->id,
            'message' => "New stock transfer request from {$this->stockTransfer->fromBranch->name} to {$this->stockTransfer->toBranch->name}",
            'created_by' => $this->stockTransfer->createdBy->username,
            'quantity' => $this->stockTransfer->quantity,
            'product' => $this->stockTransfer->inventory->product->name
        ];
    }
}
