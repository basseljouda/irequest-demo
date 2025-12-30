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

class RMAInspection extends Notification implements ShouldQueue {

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
        $details = $this->rma->itemsDetails->where('resolution_status', 'waiting customer')->map(function ($item) {
                    return $item->partRmaItem->part_title . ' #' . $item->serial_no .
                            '<br/><b>iMed Resolution:</b> ' . ucwords($item->user_resolution) .
                            '<br/><b>Notes:</b> ' . $item->user_notes . '<br/>';
                })->implode('<br>');

        return (new MailMessage)
                        ->subject('RMA Inspection - Action Required #'.$this->rma->id)
                        ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
                        ->line(__('Please review the RMA details provided below and submit your response at your earliest convenience.'))
                        ->line(__('Your prompt action will help us resolve this matter efficiently and ensure timely processing.'))
                        ->line(new \Illuminate\Support\HtmlString("<h2>RMA Items:</h2>"))
                        ->line(new \Illuminate\Support\HtmlString($details))
                        ->action(lang('View RMA'), url('/part_request/index-rma-orders?id=' . $this->rma->id))
                        ->line(__('If you have any questions or need assistance, please feel free to contact our support team.'))
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
                        ->content("Action Required for RMA #" . $this->rma->id)->unicode();
    }

}
