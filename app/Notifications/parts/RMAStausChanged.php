<?php

namespace App\Notifications\parts;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use \App\PartRMA;

class RMAStausChanged extends Notification implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $rma;

    public function __construct(PartRMA $rma) {
        $this->rma = $rma;
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
        $details = $this->rma->items->map(function ($detail) {
                    return '-'. $detail->part_title . ' - <b>RMA Qty</b>: ' . $detail->rma_qty .
                            '<br/><b>Return Reason:</b> ' . ucwords($detail->return_reason) . ' - <b>Return Type:</b> ' . $detail->return_type.'<br/>';
                })->implode('<br>');

        return (new MailMessage)
                        ->subject('RMA Status Changed #' . $this->rma->order->getCustomID())
                        ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
                        ->line('RMA ('.$this->rma->id. ') status for Order #' . $this->rma->order->getCustomID()
                                . ' changed to ' . ucwords($this->rma->status).' by ' .
                                ucwords($this->rma->user->name))
                        ->line(new \Illuminate\Support\HtmlString("<h2>RMA Details:</h2>"))
                        ->line(new \Illuminate\Support\HtmlString("<hr>"))
                        ->line(new \Illuminate\Support\HtmlString('<b>Site:</b> ' . ucwords($this->rma->order->site->name)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Contact Name:</b> ' . ucwords($this->rma->contact_name)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Contact Phone:</b> ' . ucwords($this->rma->contact_phone)))
                        ->line(new \Illuminate\Support\HtmlString('<b>Contact Email:</b> ' . ucwords($this->rma->contact_email)))
                        ->line(new \Illuminate\Support\HtmlString('<h2>RMA Parts:</h2>'))
                        ->line(new \Illuminate\Support\HtmlString($details))
                        ->action(lang('View RMA'), url('/part_request/index-rma-orders?id=' . $this->rma->id))
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
                        ->content("RMA status changed #" . $this->rma->order->getCustomID() . ' to ' . ucwords($this->rma->status))->unicode();
    }

}
