<?php

namespace App\Http\Controllers;

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

        // Pošli na tvoje FastAPI
        $response = Http::attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->asMultipart()->post($url, [
            'pages' => $pages
        ]);

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
