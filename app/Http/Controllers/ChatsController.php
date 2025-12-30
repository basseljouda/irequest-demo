<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;


class ChatsController extends Admin\AdminBaseController {

    public function __construct() {
        parent::__construct();
        
        $this->pageTitle = __('menu.orders');
        $this->pageIcon = 'fa fa-tasks';
    }

    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('chat.chat',$this->data);
    }

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function fetchMessages() {
        return Message::with('user')->get();
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request) {
        $user = Auth::user();

        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        broadcast(new MessageSent($user, $message))->toOthers();

        return ['status' => 'Message Sent!'];
    }

}
