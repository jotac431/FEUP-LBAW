<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuthAPIController extends Controller
{
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function loginWithGoogle()
    {
        $user = Socialite::driver('google')->user();
        $appUser = User::where('google_id', $user->id)
            ->orWhere('email', $user->email)->first();

        if ($appUser) {
            Auth::login($appUser);
            return redirect('/');
        } else {

            if (isset($user->avatar))
                $imgName = $this::saveAvatarFromURL($user->avatar);

            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'avatar' => isset($user->avatar) ? $imgName : null,
                'password' => encrypt($user->id),
                'birth_date' => '1970-01-01 00:00:00',
                'country_id' => 177, // Portugal by default
            ]);

            Auth::login($newUser);
            return redirect('/');
        }
    }

    private static function saveAvatarFromURL($url)
    {
        $content = file_get_contents($url);
        $imgName = round(microtime(true)*1000).'.jpg';
        Storage::put('public/avatars/'.$imgName, $content);
        return $imgName;
    }
}
