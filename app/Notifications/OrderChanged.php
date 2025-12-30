<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Nexmo\Client\Exception\Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Orders;



class OrderChanged extends Notification implements ShouldQueue
{
    use  Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $message;
    
    protected $order;
    
    public function __construct(Orders $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database', 'mail'];

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
            ->subject('Rental Order Status: #'.$this->order->order_id.' - '.ucwords(config('constant.orders.'.$this->order->status)))
            ->greeting(__('email.hello').' ' . ucwords($notifiable->name) . '!')
            ->line('Rental Order ('.$this->order->order_id.') status changed to '.ucwords(config('constant.orders.'.$this->order->status)))
            ->line('Location: '.ucwords($this->order->hospital->name))
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
        try {
        return (new NexmoMessage)
                    ->content("Order #".$this->order->order_id.': '
                            .ucwords(config('constant.orders.'.$this->order->status)).'-'.
                            ucwords($this->order->hospital->name))->unicode();
        } catch (Exception $ex){
            var_dump('error sms notification');
        }
    }
}
