<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PdfTextExtractController extends Controller
{
    public function extract(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $file = $request->file('file');

        $url = config('pdf.base_url') . '/extract-text';

        $user = $request->user();
        $apiToken = optional($user->frontendToken())->key;

        $response = Http::withHeaders([
            'x-api-key' => $apiToken,
        ])
        ->attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )
        ->asMultipart()
        ->post($url);

        if (!$response->ok()) {
            return response()->json(['error' => 'Text extraction failed'], 500);
        }

        $filename = 'extracted_' . time() . '.txt';
        $path = storage_path('app/public/' . $filename);
        file_put_contents($path, $response->body());

        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}
