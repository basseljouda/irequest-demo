<?php

namespace App\Notifications\parts;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NewRequestOrder extends Notification implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $order;

    public function __construct(\App\OrderRequest $order) {
        $this->order = $order;
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
        $details = $this->order->partrequest->details->map(function ($detail) {
                    return $detail->part_title . ' - <b>Quantity</b>: ' . $detail->qty .
                            ' - <b>Condition:</b> ' . ucwords($detail->price_type) . ' - <b>Unit Price:</b> $' . $detail->part_price;
                })->implode('<br>');

        return (new MailMessage)
                        ->subject('New Part Order: #' . $this->order->getCustomID())
                        ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
                        ->line('New Part Order: #' . $this->order->getCustomID() . ' by: ' .
                                ucwords($this->order->user->name))
                ->line(new \Illuminate\Support\HtmlString("<hr>"))
                        ->line(new \Illuminate\Support\HtmlString("<h2>Order Details:</h2>"))
                        ->line(new \Illuminate\Support\HtmlString('<b>Site:</b> ' . ucwords($this->order->site->name)))
                        ->line(new \Illuminate\Support\HtmlString('<b>PO Number:</b> ' . ucwords($this->order->po_number)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Shipping Address:</b> ' .
                                        ucwords($this->order->address . ' ' . $this->order->city . ' ' . $this->order->state . ' ' . $this->order->zip_code . ' ' . $this->order->country)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Shipping Type:</b> ' . ucwords($this->order->shipment_type)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Shipping Method:</b> ' . ucwords($this->order->shipment_method)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Contact Name:</b> ' . ucwords($this->order->partRequest->contact_name)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Contact Phone:</b> ' . ucwords($this->order->partRequest->contact_phone)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Contact Email:</b> ' . ucwords($this->order->partRequest->contact_email)))
                        ->line(new \Illuminate\Support\HtmlString('<h2>Parts List:</h2>'))
                        ->line(new \Illuminate\Support\HtmlString($details))
                        ->action(lang('View Order'), url('/part_request/index-order?id=' . $this->order->id))
                        ->line(__('email.thankyouNote'));
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
                        ->content("New Part Order #" . $this->order->getCustomID() . ' - ' . ucwords($this->order->site->name))->unicode();
    }

}
