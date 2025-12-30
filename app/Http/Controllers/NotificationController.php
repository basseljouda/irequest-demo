<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\Admin\AdminBaseController;
use Illuminate\Http\Request;

class NotificationController extends AdminBaseController {

    public function markAllRead() {
        foreach ($this->user->unreadNotifications->take(20) as $notification) {
            $notification->markAsRead();
        }
        return Reply::success(__('messages.notificationRead'));
    }

}
