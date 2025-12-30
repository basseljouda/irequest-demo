<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NewRegistration extends Notification implements ShouldQueue
{
    use  Dispatchable, InteractsWithQueue, Queueable ;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $name;
    
    protected $phone;


    public function __construct($name,$phone)
    {
        $this->name = $name;
        $this->phone = $phone;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
        'data' => array_merge($notifiable->toArray(), ['name' => $this->name, 'phone'=> $this->phone])
        ];
    }
}
