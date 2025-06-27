<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Jyj1993126\NameThatColorPhp\ColorNamer;

class ColorController extends Controller
{
    public function detect(Request $request)
    {
        // 1) Validate & read the uploaded image
        $request->validate([
            'image' => 'required|image|max:5120', // up to 5 MB
        ]);
        $path       = $request->file('image')->getRealPath();
        $imageBytes = file_get_contents($path);

        // 2) Build Azure Color API URL
        $endpoint = config('services.azure_cs.endpoint');
        $url      = rtrim($endpoint, '/') . '/vision/v3.2/analyze?visualFeatures=Color';

        // 3) Call Azure REST API
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => config('services.azure_cs.key'),
            'Content-Type'             => 'application/octet-stream',
        ])
        ->timeout(30)
        ->withBody($imageBytes, 'application/octet-stream')
        ->post($url);


        if (! $response->ok()) {
            return response()->json([
                'error' => 'Azure API error: ' . $response->body(),
            ], 500);
        }

        $data = $response->json();

        // 4) Extract Azure’s dominant color info
        $hexDetected = $data['color']['dominantColorForeground'] ?? null;   // e.g. "#336699"
        $aiName      = $data['color']['dominantColors'][0] ?? null;         // e.g. "Blue"

        // 5) Use name-that-color to get a human-friendly match
        $namer   = new ColorNamer();
        $closest = $namer->name($hexDetected);

        // 6) Return JSON payload
        return response()->json([
            'hex_detected'    => $hexDetected,
            'ai_generic_name' => $aiName,
            'closest_hex'     => $closest['hex'],
            'closest_name'    => $closest['name'],
            'exact_match'     => $closest['exact'],
        ]);
    }
}
