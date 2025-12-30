<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class BaseNotification extends Notification
{
    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend($notifiable, $channel)
    {
        if (app()->environment('development')) {
            // Allow only specific email in development mode
            return $notifiable->routeNotificationFor('mail') === '1basseljouda@gmail.com';
        }

        // In production, allow all notifications
        return false;
    }
}
