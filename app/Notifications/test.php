<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Request;


class test extends Notification implements ShouldQueue
{
    use  Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $order;
    
    protected $clientIP;


    public function __construct(\App\Orders $order,$clientIP)
    {
        $this->order = $order;
        $this->clientIP = $clientIP;
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

        /*if (\App\SmsSetting::first()->nexmo_status == 'active') {
            array_push($via, 'nexmo');   
        }*/

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
            ->subject('Testing New Rental Order: #'.$this->order->order_id)
            ->greeting(__('email.hello').' ' . ucwords($notifiable->name) . '!')            
            ->line(user()->name ?? 'none')
            ->line(config('app.url'))
            ->line($this->clientIP);
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
                    ->content("Test Order #".$this->order->order_id.' - '.ucwords($this->order->hospital->name))->unicode();
    }
}
