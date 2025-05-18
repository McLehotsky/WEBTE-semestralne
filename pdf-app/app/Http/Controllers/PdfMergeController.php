<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class PdfMergeController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file1' => 'required|file|mimes:pdf',
            'file2' => 'required|file|mimes:pdf',
        ]);
    
        $client = new \GuzzleHttp\Client();

        $url = config('pdf.base_url') . '/merge';

        $user = $request->user();

        $apiToken = optional($user->frontendToken())->key;
    
        $response = $client->post($url, [
            'headers' => [
                'x-api-key' => $apiToken,
            ],
            'multipart' => [
                [
                    'name'     => 'file1',
                    'contents' => fopen($request->file('file1')->getPathname(), 'r'),
                    'filename' => 'file1.pdf',
                ],
                [
                    'name'     => 'file2',
                    'contents' => fopen($request->file('file2')->getPathname(), 'r'),
                    'filename' => 'file2.pdf',
                ],
            ],
            'stream' => true,
        ]);
    
        $filename = 'merged_' . time() . '.pdf';
        $path = storage_path('app/public/' . $filename);
    
        // zapíš PDF obsah do súboru
        file_put_contents($path, $response->getBody()->getContents());
    
        // vráť URL na stiahnutie
        return response()->json([
            'url' => asset('storage/' . $filename),
        ]);
    }
}
