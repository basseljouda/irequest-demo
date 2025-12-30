<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */

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
        parent::__construct();
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request) {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::getLastAttempted();
            if (!$user->role) { // no role attached
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'Your account is Disabled, please contact support']);
            } else {
                $request->session()->regenerate();

                return redirect()->intended('admin/dashboard');
            }
        }

        return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showLoginForm() {
        $hospitals = \App\Hospitals::all();
        $titles = \App\StaffTitle::all();
        $registered = false;
        return view('auth.login', compact('hospitals', 'registered', 'titles'));
    }

    public function showRegistered() {
        $registered = true;
        return view('auth.login', compact('registered'));
    }

    protected function redirectTo() {
        return 'admin/dashboard';
    }

    public function logout(Request $request) {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showPrivacyPolicy() {
        return view('layouts.privacy-policy');
    }
    
    public function showTermsConditions() {
        return view('layouts.terms-conditions');
    }

}
