<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordResetController extends Controller
{   
    use ResetsPasswords;
    
    protected $redirectTo = '/';

    public function __construct() {
        $this->middleware('guest');
    }

    public function showSendLinkForm() {
        return view('auth.passwords.forgetPassword');
    }

    public function sendLink(Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ?   back()->with(['status' => __($status)])
            :   back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm() {
        return view('auth.passwords.resetPassword');
    }
 
}
