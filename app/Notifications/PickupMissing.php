<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PickupMissing extends Notification implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $order;
    protected $user;
    protected $equipmentList;
    protected $request;

    public function __construct(\App\Orders $order, $user, $pickupIssues) {
        $this->order = $order;
        $this->user = $user;
        $this->issuesCount = count($pickupIssues);

        // Format issues list
        $this->issuesList = [];
        foreach ($pickupIssues as $issue) {
            $equipmentName = $issue->orderEquipment->equipment->name ?? 'Unknown Equipment';
            $details = $issue->missing_details;
            $this->issuesList[] = "<strong>{$equipmentName}:</strong><br/>{$details}";
        }
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
        return (new MailMessage)
                        ->subject('Pickup Issue Reported: Order #' . $this->order->order_id)
                        ->greeting(__('email.hello') . ' ' . ucwords($notifiable->name) . '!')
                        ->line('⚠️ **Issue Alert** ⚠')
                        ->line('Missing/damaged items were reported during pickup for order #' . $this->order->order_id)
                        ->line('Reported by: ' . ucwords($this->user->name))
                        ->line('Site: ' . ucwords($this->order->hospital->name))
                        ->line('Patient Name: ' . ucwords($this->order->patient_name))
                        ->line('Room No: ' . ucwords($this->order->room_no))
                        ->line('Items with Issues:')
                        ->line(new \Illuminate\Support\HtmlString(implode('<br>', $this->issuesList)))
                        ->line('**Total Issues Reported:** ' . $this->issuesCount)
                        ->action(__('Review Issues'), url('/orders/pickup_issues'))
                        ->line('Please review and resolve these issues as soon as possible.')
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
                        ->content("Pickup Issue Reported: Order #" . $this->order->order_id . ' - ' . ucwords($this->order->hospital->name))->unicode();
    }

}
