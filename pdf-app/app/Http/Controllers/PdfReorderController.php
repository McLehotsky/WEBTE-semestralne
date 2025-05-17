<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PdfReorderController extends Controller
{
    public function reorder(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
            'order' => 'required|string', // napr. "2,0,1"
        ]);

        $file = $request->file('file');
        $order = $request->input('order');

        $url = config('pdf.base_url') . '/reorder';

        $response = Http::attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->asMultipart()->post($url, [
            'order' => $order
        ]);

        if (!$response->ok()) {
            return response()->json(['error' => 'Reorder failed'], 500);
        }

        $filename = 'reordered_' . time() . '.pdf';
        $path = storage_path('app/public/' . $filename);
        file_put_contents($path, $response->getBody()->getContents());

        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}
