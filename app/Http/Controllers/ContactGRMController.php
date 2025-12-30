<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\Reply;
use Mail;
use Config;

class ContactGRMController extends Admin\AdminBaseController {
    
    use \App\Traits\SmtpSettings,        \App\Traits\SmsSettings;

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Contact Us';
        $this->pageIcon = 'fa fa-phone-square';
        
        $this->setMailConfigs();
        $this->setSmsConfigs();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        abort_if(!$this->user->can('view_contact_grm'), 403);
        $this->users = \App\User::where('show_in_contact', 1)->get();
        /* \App\User
          ::whereNotIn('id', \App\HospitalsStuff::where("user_id",'>','0')->pluck('user_id')->toArray())
          ->get(); */
        return view('contacts.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $caller = $request->caller;
        
        if (strpos($caller, '@') !== false) {
            
            Mail::send('email.grmcontact', ['name' => user()->name, "msg" => $request->msg], function($message) use ($caller) {
                $message->to($caller, 'Rental User')->subject('User Message');
                $message->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
            });
            return Reply::success('Email sent successfully');
            
        } else {
            
            try {

                $basic = new \Nexmo\Client\Credentials\Basic(
                        Config::get('nexmo.api_key'), Config::get('nexmo.api_secret'));
                $client = new \Nexmo\Client($basic);

                $message = $client->message()->send([
                    'to' => $caller,
                    'from' => Config::get('services.nexmo.sms_from'),
                    'text' => $request->msg
                ]);

                return Reply::success('Message sent successfully');
            } catch (Exception $e) {
                return Reply::error("Error: " . $e->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ContactGRMController  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function show() {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ContactGRMController  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactGRMController $costCenter) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContactGRMController  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactGRMController $costCenter) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContactGRMController  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactGRMController $costCenter) {
        //
    }

}
