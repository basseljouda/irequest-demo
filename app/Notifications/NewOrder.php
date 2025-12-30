<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * DEMO SKELETON: New Order Notification
 * 
 * This notification was originally responsible for:
 * - Sending notifications when new orders are created
 * - Sending via email, database, and SMS (Nexmo)
 * - Including order details and links
 * 
 * For demo purposes, notification structure is kept but actual sending is disabled.
 * In production, this would send real notifications.
 */
class NewOrder extends Notification implements ShouldQueue
{
    use  Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $order;
    
    public function __construct(\App\Orders $order)
    {
        $this->order = $order;
    }

    /**
     * DEMO: Get the notification's delivery channels
     * Original: Checked SMS settings and returned appropriate channels
     */
    public function via($notifiable)
    {
        // DEMO: Return database only (no actual email/SMS sending)
        return ['database'];
        
        // Original logic:
        // - Checked SMS settings from database
        // - Returned ['database', 'mail', 'nexmo'] based on settings
    }

    /**
     * DEMO: Get the mail representation of the notification
     * Original: Created email with order details and action link
     */
    public function toMail($notifiable)
    {
        // DEMO: Return basic mail message (not actually sent)
        return (new MailMessage)
            ->subject('New Rental Order: #DEMO-001')
            ->greeting(__('email.hello').' ' . ucwords($notifiable->name ?? 'User') . '!')
            ->line('New Rental Order created (DEMO MODE)')
            ->line('Location: Demo Hospital')
            ->action(lang('View Order'), url('/orders?id=1'))
            ->line(__('email.thankyouNote'));
    }

    /**
     * DEMO: Get the array representation of the notification
     * Original: Returned order data array
     */
    public function toArray($notifiable)
    {
        // DEMO: Return dummy data
        return [
            'data' => [
                'id' => $this->order->id ?? 1,
                'order_id' => $this->order->order_id ?? 'DEMO-001',
                'message' => 'New order created (DEMO)'
            ]
        ];
    }

    /**
     * DEMO: Get the Nexmo / SMS representation of the notification
     * Original: Created SMS message with order details
     */
   public function toNexmo($notifiable)
    {
        // DEMO: Return dummy SMS message (not actually sent)
        return (new NexmoMessage)
                    ->content("New Order #DEMO-001 - Demo Hospital (DEMO MODE)")->unicode();
    }
}
