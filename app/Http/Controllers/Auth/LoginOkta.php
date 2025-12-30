<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginOkta extends Controller {

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Okta authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider() {
        return Socialite::driver('okta')->redirect();
    }

    /**
     * Obtain the user information from Okta.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request) {
        $user = Socialite::driver('okta')->user();

        $localUser = User::where('email', $user->email)->first();

        // Create a local user with the email and token from Okta
        if (!$localUser) {
            return redirect('/login');
        } else {
            $localUser->token = $user->token;
            $localUser->save();
        }

        try {
            Auth::login($localUser);
            $request->session()->regenerate();

            return redirect()->intended('admin/dashboard');
        } catch (\Throwable $e) {
            return redirect('/login/okta');
        }
    }

}
