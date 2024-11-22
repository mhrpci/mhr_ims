<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\StockTransfer;

class StockTransferApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $stockTransfer;

    public function __construct(StockTransfer $stockTransfer)
    {
        $this->stockTransfer = $stockTransfer;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Stock Transfer Request Approved')
            ->line('Your stock transfer request has been approved.')
            ->line('Details:')
            ->line('Product: ' . $this->stockTransfer->inventory->product->name)
            ->line('Quantity: ' . $this->stockTransfer->quantity)
            ->line('From: ' . $this->stockTransfer->fromBranch->name)
            ->line('To: ' . $this->stockTransfer->toBranch->name)
            ->action('View Transfer', route('stock_transfers.show', $this->stockTransfer))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'stock_transfer_id' => $this->stockTransfer->id,
            'message' => 'Stock transfer request has been approved',
            'product_name' => $this->stockTransfer->inventory->product->name,
            'quantity' => $this->stockTransfer->quantity,
            'from_branch' => $this->stockTransfer->fromBranch->name,
            'to_branch' => $this->stockTransfer->toBranch->name,
        ];
    }
}
