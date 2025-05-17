<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ApiKey;

class ApiKeyController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        // Deaktivuj len tokeny typu 'api'
        $user->apiKeys()
            ->where('type', 'api')
            ->update(['active' => false]);

        do {
            $newToken = Str::random(50);
        } while (ApiKey::where('key', $newToken)->exists());

        $apiKey = $user->apiKeys()->create([
            'key' => $newToken,
            'type' => 'api',
            'active' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'token' => $apiKey->key,
            ]);
        }

        return back()->with([
            'status' => 'token-generated',
            'api_token' => $apiKey->key,
        ]);
    }
}
