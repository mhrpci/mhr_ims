<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\StockIn;

class NearExpiryNotification extends Notification
{
    use Queueable;

    protected $stockIn;

    public function __construct(StockIn $stockIn)
    {
        $this->stockIn = $stockIn;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $daysUntilExpiry = now()->diffInDays($this->stockIn->expiration_date);
        
        return (new MailMessage)
            ->subject('Near Expiry Stock Alert')
            ->line('Stock item is approaching expiration:')
            ->line("Product: {$this->stockIn->product->name}")
            ->line("Lot Number: {$this->stockIn->lot_number}")
            ->line("Branch: {$this->stockIn->branch->name}")
            ->line("Expiration Date: {$this->stockIn->expiration_date}")
            ->line("Days until expiry: {$daysUntilExpiry}")
            ->action('View Stock In', route('stock_ins.show', $this->stockIn->id));
    }

    public function toArray($notifiable)
    {
        return [
            'stock_in_id' => $this->stockIn->id,
            'product_name' => $this->stockIn->product->name,
            'lot_number' => $this->stockIn->lot_number,
            'branch_name' => $this->stockIn->branch->name,
            'expiration_date' => $this->stockIn->expiration_date,
            'days_until_expiry' => now()->diffInDays($this->stockIn->expiration_date)
        ];
    }
} 