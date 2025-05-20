<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PdfAddPageController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'base' => 'required|file|mimes:pdf|max:20480',
            'insert' => 'required|file|mimes:pdf|max:20480',
            'position' => 'required|integer|min:0',
        ]);

        $url = config('pdf.base_url') . '/add-page';

        $user = $request->user();

        $apiToken = optional($user->frontendToken())->key;
        Log::warning('Toto je varovanie', ['url' => $url]);

        $response = Http::withHeaders([
                'x-api-key' => $apiToken,
        ])
        ->attach(
            'base',
            file_get_contents($request->file('base')->getRealPath()),
            $request->file('base')->getClientOriginalName()
        )->attach(
            'insert',
            file_get_contents($request->file('insert')->getRealPath()),
            $request->file('insert')->getClientOriginalName()
        )->asMultipart()->post($url, [
            'position' => $request->input('position'),
        ]);

        Log::info('Odpoveď z /add-page', [
            'status' => $response->status(),
            'body' => $response->body(), // textové telo odpovede
        ]);
        if (!$response->ok()) {
            return response()->json(['error' => 'Add page failed'], 500);
        }

        $filename = 'addpage_' . time() . '.pdf';
        $path = storage_path('app/public/' . $filename);
        file_put_contents($path, $response->getBody());

        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}