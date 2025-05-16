<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PdfEncryptController extends Controller
{
    public function encrypt(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'password' => 'required|string|min:3',
        ]);
    
        $client = new Client();
    
        $response = $client->post('https://node23.webte.fei.stuba.sk/api/pdf/encrypt', [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => fopen($request->file('file')->getPathname(), 'r'),
                    'filename' => 'file.pdf',
                ],
                [
                    'name'     => 'password',
                    'contents' => $request->input('password'),
                ],
            ],
            'stream' => true,
        ]);
    
        $filename = 'encrypted_' . time() . '.pdf';
        $path = storage_path('app/public/' . $filename);
        file_put_contents($path, $response->getBody()->getContents());
    
        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}
