<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use App\Models\LoginHistory;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        $ip = Request::ip();
        $agent = Request::header('User-Agent');
        $geo = Http::get("http://ip-api.com/json/{$ip}")->json();

        LoginHistory::create([
            'user_id' => $event->user->id,
            'ip_address' => $ip,
            'city' => $geo['city'] ?? null,
            'country' => $geo['country'] ?? null,
            'user_agent' => $agent,
            'logged_in_at' => now(),
        ]);
    }
}
