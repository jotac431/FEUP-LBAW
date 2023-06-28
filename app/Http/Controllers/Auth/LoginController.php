<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function getUser()
    {
        return $request->user();
    }

    public function home()
    {
        return redirect('login');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->is_suspended) {
            Auth::logout();
            $suspension = $user->suspensionEndInfo();

            return back()->withErrors([
                'suspended' => 'Your account has been suspended by an administrator',
                'reason' => $suspension['reason'],
                'endDate' => $suspension['end_date']
            ]);
        }

        return redirect()->intended($this->redirectPath());
    }
}
