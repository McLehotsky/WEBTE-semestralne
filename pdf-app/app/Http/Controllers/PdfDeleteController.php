<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PdfDeleteController extends Controller
{
    public function show()
    {
        return view('pdf.delete');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'pages' => 'required|string'
        ]);

        $client = new Client([
            'timeout' => 20,
        ]);

        $url = config('pdf.base_url') . '/decrypt';

        $response = $client->post($url, [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($request->file('file')->getPathname(), 'r'),
                    'filename' => 'file.pdf'
                ],
                [
                    'name' => 'pages',
                    'contents' => $request->input('pages')
                ]
            ],
            'stream' => true,
        ]);

        $filename = 'deleted_' . time() . '.pdf';
        $path = storage_path('app/public/' . $filename);
        file_put_contents($path, $response->getBody()->getContents());

        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}
