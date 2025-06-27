<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use ourcodeworld\NameThatColor\ColorInterpreter;

class ColorController extends Controller
{
    public function detect(Request $request)
    {
        // 1) Validation et lecture de l’image uploadée
        $request->validate([
            'image' => 'required|image|max:5120', // jusqu’à 5 Mo
        ]);
        $path       = $request->file('image')->getRealPath();
        $imageBytes = file_get_contents($path);

        // 2) Construction de l’URL Azure Color API
        $endpoint = config('services.azure_cs.endpoint');
        $url      = rtrim($endpoint, '/') . '/vision/v3.2/analyze?visualFeatures=Color';

        // 3) Envoi du flux binaire à Azure (sans json_encode)
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

        // 4) Extraction des infos de couleur dominante
        $hexDetected = $data['color']['dominantColorForeground'] ?? null; // ex. "#336699"
        $aiName      = $data['color']['dominantColors'][0] ?? null;       // ex. "Blue"

        // 5) Recherche de la couleur la plus proche
        $interpreter = new ColorInterpreter();
        $closest     = $interpreter->name(ltrim($hexDetected, '#'));

        // 6) Réponse JSON
        return response()->json([
            'hex_detected'    => $hexDetected,
            'ai_generic_name' => $aiName,
            'closest_hex'     => $closest['hex'],
            'closest_name'    => $closest['name'],
            'exact_match'     => $closest['exact'],
        ]);
    }
}
