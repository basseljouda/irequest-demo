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
use App\ticket;



class TicketAssigned extends Notification implements ShouldQueue
{
    use  Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $message;
    
    protected $ticket;
    
    public function __construct(ticket $ticket)
    {
        $this->ticket = $ticket;
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
            ->subject('Ticket Assignment # '.$this->ticket->id)
            ->greeting(__('email.hello').' ' . ucwords($notifiable->name) . '!')
            ->line('Ticket (#'.$this->ticket->id.') has been assigned to '.$this->ticket->assigned->email)
            ->line('Notes: "'.$this->ticket->assign_notes.'"')
            ->action(lang('View Ticket'), url('/ticket?id='.$this->ticket->id))
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
            'data' => $this->ticket->toArray()
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
                    ->content("Ticket (#".$this->ticket->id.") has been assigned to".$this->ticket->assigned->name)->unicode();
        } catch (Exception $ex){
            var_dump('error tickets assign notification');
        }
    }
}