<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class PdfAddPageController extends Controller
{
    public function show()
    {
        return view('pdf.add-page');
    }

    public function add(Request $request)
    {
        $request->validate([
            'base_file' => 'required|file|mimes:pdf|max:10240',
            'insert_file' => 'required|file|mimes:pdf|max:10240',
            'position' => 'required|integer|min:0'
        ]);

        $client = new Client(['timeout' => 20]);
        $url = config('pdf.base_url') . '/add-page';

        $response = $client->post($url, [
            'multipart' => [
                [
                    'name' => 'base_file',
                    'contents' => fopen($request->file('base_file')->getPathname(), 'r'),
                    'filename' => 'base.pdf'
                ],
                [
                    'name' => 'insert_file',
                    'contents' => fopen($request->file('insert_file')->getPathname(), 'r'),
                    'filename' => 'insert.pdf'
                ],
                [
                    'name' => 'position',
                    'contents' => (string) $request->input('position')
                ]
            ],
            'stream' => true
        ]);

        $filename = 'added_' . time() . '.pdf';
        $path = storage_path('app/public/' . $filename);
        file_put_contents($path, $response->getBody()->getContents());

        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}
