<?php

namespace App\Notifications\parts;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RequestRejected extends Notification implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $partRequest;

    public function __construct(\App\PartRequest $partRequest) {
        $this->partRequest = $partRequest;
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
        $details = $this->partRequest->details->map(function ($detail) {
                    return $detail->part_title . ' - <b>Quantity</b>: ' . $detail->qty .
                            ' - <b>Condition:</b> ' . ucwords($detail->price_type) . ' - <b>Unit Price:</b> $' . $detail->part_price;
                })->implode('<br>');

        return (new MailMessage)
                        ->subject('RFQ Rejected: #' . $this->partRequest->id)
                        ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
                        ->line('RFQ Rejected by: ' . 
                                ucwords($this->partRequest->rfqRejectedBy->name))
                        ->line(new \Illuminate\Support\HtmlString("<h2>Reject Reason:</h2>"))
                        ->line(new \Illuminate\Support\HtmlString("<h2>".$this->partRequest->rfq_reject_reason."</h2>"))
                        ->line(new \Illuminate\Support\HtmlString("<hr>"))
                        ->line(new \Illuminate\Support\HtmlString("<h2>Request Details:</h2>"))
                        ->line(new \Illuminate\Support\HtmlString('<b>Site:</b> ' . ucwords($this->partRequest->hospital->name)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Contact Name:</b> ' . ucwords($this->partRequest->contact_name)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Contact Phone:</b> ' . ucwords($this->partRequest->contact_phone)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Contact Email:</b> ' . ucwords($this->partRequest->contact_email)))
                        ->line(new \Illuminate\Support\HtmlString('<h2>Parts List:</h2>'))
                        ->line(new \Illuminate\Support\HtmlString($details))
                        ->line(new \Illuminate\Support\HtmlString('<b>Notes:</b> ' . ucwords($this->partRequest->notes)))
                        ->action(lang('View Request'), url('/part_request?highlight_id=' . $this->partRequest->id))
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
            'data' => $this->partRequest->toArray()
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
                        ->content("RFQ Rejected by: " . ucwords($this->partRequest->rfqRejectedBy->name))->unicode();
    }

}
