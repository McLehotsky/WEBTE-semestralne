<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;


class SetLocale
{
    // public function handle($request, Closure $next)
    // {
    //     App::setLocale(Session::get('locale', config('app.locale')));
    //     return $next($request);
    // }


public function handle($request, \Closure $next)
{
    $locale = session('locale') ?? $request->cookie('locale') ?? config('app.locale');

    if (!in_array($locale, ['sk', 'en'])) {
        $locale = config('app.locale');
    }

    app()->setLocale($locale);

    \Log::info("🟢 SetLocale middleware: session=" . session('locale') . ", cookie=" . $locale . ", app=" . app()->getLocale());

    return $next($request);
}




}
