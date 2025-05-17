<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PdfRotateController extends Controller
{
    public function show()
    {
        return view('pdf.rotate');
    }

    public function rotate(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
            'rotations' => 'required|string'
        ]);

        $client = new Client([
            'timeout' => 20,
        ]);

        $url = config('pdf.base_url') . '/rotate';

        $response = $client->post($url, [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($request->file('file')->getPathname(), 'r'),
                    'filename' => 'file.pdf'
                ],
                [
                    'name' => 'rotations',
                    'contents' => $request->input('rotations')
                ]
            ],
            'stream' => true,
        ]);

        $filename = 'rotated_' . time() . '.pdf';
        $path = storage_path('app/public/' . $filename);
        file_put_contents($path, $response->getBody()->getContents());

        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}
