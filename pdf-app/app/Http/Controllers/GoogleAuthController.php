<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Login;
use App\Models\ApiKey;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?? $googleUser->getNickname(),
                'password' => bcrypt(Str::random(16)), // náhodné heslo
            ]
        );

        Auth::login($user);

        event(new Login('web', $user, false));

        if (!$user->apiKeys()->where('type', 'frontend')->exists()) {
            $user->apiKeys()->create([
                'key' => Str::random(60),
                'type' => 'frontend',
                'active' => true,
            ]);
        }

        return redirect()->intended('/dashboard');
    }
}
