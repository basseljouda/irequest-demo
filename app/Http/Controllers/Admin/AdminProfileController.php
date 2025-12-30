<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\UpdateProfile;
use App\SmsSetting;
use App\User;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('Profile setting');
        $this->pageIcon = 'ti-user';
    }

    public function index()
    {
        return view('admin.profile.index', $this->data);
    }

    public function update(UpdateProfile $request)
    {

        $user = User::find($this->user->id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password != '') {
            $user->password = Hash::make($request->password);
        }

        $user->mobile = $request->mobile;
        $user->calling_code = $request->calling_code;

        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image, 'profile');
        }

        $user->save();

        return Reply::redirect(route('admin.profile.index'), __('menu.myProfile') . ' ' . __('messages.updatedSuccessfully'));
    }
}
