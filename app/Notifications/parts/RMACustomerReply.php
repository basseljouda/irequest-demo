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

class RMACustomerReply extends Notification implements ShouldQueue {

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
        $details = $this->rma->itemsDetails->where('resolution_status', 'customer replied')->map(function ($item) {
                    return $item->partRmaItem->part_title . ' #' . $item->serial_no .
                            '<br/>iMed Resolution: ' . ucwords($item->user_resolution) .
                            '<br/><b>Customer Response:</b> ' . ucwords($item->customer_resolution).
                            '<br/><b>Customer Notes:</b> ' . $item->customer_Notes;
                })->implode('<br>');

        return (new MailMessage)
                        ->subject('RMA Inspection - Customer Reply #'.$this->rma->id)
                        ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
                        ->line(__('The customer response for RMA #'. $this->rma->id. ' has been successfully submitted.'))
                        ->line(new \Illuminate\Support\HtmlString("<h2>Customer Response for RMA items:</h2>"))
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
                        ->content("Customer reply submitted for RMA #" . $this->rma->id)->unicode();
    }

}
