<?php

namespace App\Notifications\parts;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderShippingUpdate extends Notification implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $order;
    protected $trackingInfo;

    public function __construct(\App\OrderRequest $order, $trackingInfo) {
        $this->order = $order;
        $this->trackingInfo = $trackingInfo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        $via = ['mail'];

        if (\App\SmsSetting::first()->nexmo_status == 'active') {
            array_push($via, 'nexmo');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {

        $trackingHistory = $this->trackingInfo['tracking_history'] ?? [];
        $carrier = strtoupper($this->trackingInfo['carrier'] ?? 'N/A');
        $trackingNumber = $this->trackingInfo['tracking_number'] ?? 'N/A';
        $metadata = $this->trackingInfo['metadata'] ?? 'N/A';
        $status = $this->trackingInfo['tracking_status']['status'] ?? 'N/A';
        $eta = dformat($this->trackingInfo['eta'] ?? 'N/A');
        $toAddress = implode(', ', array_filter([
        $this->trackingInfo['address_to']['city'] ?? 'N/A',
        $this->trackingInfo['address_to']['state'] ?? 'N/A',
        $this->trackingInfo['address_to']['zip'] ?? 'N/A',
        $this->trackingInfo['address_to']['country'] ?? 'N/A'
    ]));


        // Build the email
        $mailMessage = (new MailMessage)
                ->subject('New Shipping Status Update - Order #' . $this->order->getCustomID())
                ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
                ->line(new \Illuminate\Support\HtmlString('Order: #' . $this->order->getCustomID() . ' Tracking Status changed to <strong>' . $this->order->shipment->tracking_status).'</strong>')
                ->line('Carrier: ' . $carrier)
                ->line('Tracking Number: ' . $trackingNumber)
                ->line('Destination Address: ' . $toAddress)
                ->line('Estimated Delivery Date: ' . $eta)
                ->line('Status: ' . $status);

        // Add tracking updates if available
        if (!empty($trackingHistory)) {
            $mailMessage->line(new \Illuminate\Support\HtmlString('<strong>Tracking Updates:</strong>'));
            foreach ($trackingHistory as $update) {
                $date = \Carbon\Carbon::parse($update['status_date'] ?? '')->toFormattedDateString();
                $statusDetail = $update['status_details'] ?? 'N/A';
                $mailMessage->line(new \Illuminate\Support\HtmlString("- <strong>{$date}:</strong> {$update['status']} ({$statusDetail})"));
            }
        }

        $mailMessage->action(lang('View Order'), url('/part_request/index-order?id=' . $this->order->id))
                ->line(__('email.thankyouNote'));

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
            'data' => $this->order->toArray()
        ];
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable) {
        return (new NexmoMessage)
                        ->content("Shipping Status Update #" . $this->order->getCustomID() . ': ' . $this->order->shipment->tracking_status)->unicode();
    }

}
