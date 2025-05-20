<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfExtractController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
            'pages' => 'required|string',
        ]);

        $file = $request->file('file');
        $pages = $request->input('pages');
        
        $url = config('pdf.base_url') . '/extract';
        Log::warning('Toto je varovanie', ['url' => $url]);

        $user = $request->user();

        $apiToken = optional($user->frontendToken())->key;

        // Pošli na tvoje FastAPI
        $response = Http::withHeaders([
                'x-api-key' => $apiToken,
        ])
        ->attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )
        ->asMultipart()
        ->post($url, [
            'pages' => $pages,
        ]);

        Log::warning('Toto je varovanie', ['response' => $response->body()]);
        if (!$response->ok()) {
            return response()->json(['error' => 'Extraction failed'], 500);
        }

        // Ulož PDF
        $filename = 'extracted_' . time() . '.pdf';
        $path = storage_path('app/public/' . $filename);
        file_put_contents($path, $response->getBody()->getContents());
    
        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}
