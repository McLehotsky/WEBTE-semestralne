<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PdfSplitController extends Controller
{
    public function split(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
            'chunk_size' => 'required|integer|min:1',
        ]);

        $file = $request->file('file');
        $chunkSize = $request->input('chunk_size');

        $response = Http::attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->asMultipart()->post('https://node23.webte.fei.stuba.sk/api/pdf/split', [
            'chunk_size' => $chunkSize,
        ]);

        if (!$response->ok()) {
            return response()->json(['error' => 'Split failed'], 500);
        }

        $filename = 'split_' . time() . '.zip';
        $path = storage_path('app/public/' . $filename);
        file_put_contents($path, $response->getBody()->getContents());

        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}
