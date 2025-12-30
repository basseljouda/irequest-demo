<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NewUser extends Notification implements ShouldQueue
{
    use  Dispatchable, InteractsWithQueue, Queueable ;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $password;
    
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject(__('email.newUser.subject'))
            ->greeting(__('email.hello').' ' . ucwords($notifiable->name) . ',')
            ->line("Welcome to our Team, we are glad to inform you that your registration request is approved, you can start using our services using the following login details:")
            ->line(__('app.email').' : '.$notifiable->email)
            ->line(__('app.password').' : '.$this->password)
            ->action(__('email.loginDashboard'), url('/login'))
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
            'data' => $notifiable->toArray()
        ];
    }
}
