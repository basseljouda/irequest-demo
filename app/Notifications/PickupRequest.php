<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class PickupRequest extends Notification implements ShouldQueue
{
    use  Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $order;
    
    protected $user;
    
    protected $equipmentList;
    
    protected $request;



    public function __construct(\App\Orders $order,$user,$equipmentList,$request)
    {
        $this->order = $order;
        $this->user = $user;
        $this->equipmentList = $equipmentList;
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
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
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pickup Request: Order No #'.$this->order->order_id)
            ->greeting(__('email.hello').' ' . ucwords($notifiable->name) . '!')
            ->line('This is a Pickup request for order #'.$this->order->order_id.' requested by: '.ucwords($this->user->name))
            ->line('Location: '.ucwords($this->order->hospital->name))
            ->line('Patient Name: '.ucwords($this->order->patient_name))
            ->line('Room No: '.ucwords($this->order->room_no))
            ->line('Equipment List:'. PHP_EOL)
            ->line(new \Illuminate\Support\HtmlString(implode('<br>', $this->equipmentList)))
            ->line('Pickup Location: '.ucwords($this->request["pickup_location"]))
            ->line('Contact Phone: '.ucwords($this->request["contact_phone"]))
            ->line('Notes: '.ucwords($this->request["notes"]))
            ->action(lang('View Order'), url('/orders?id='.$this->order->id))
            ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
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
   public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->content("Pickup request: Order #".$this->order->order_id.' - '.ucwords($this->order->hospital->name))->unicode();
    }
}
