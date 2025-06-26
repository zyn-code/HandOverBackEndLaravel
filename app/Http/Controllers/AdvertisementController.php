<?php
namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'services' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ]);

        $advertisement = Advertisement::create($validated);

        return response()->json(['message' => 'Advertisement created', 'data' => $advertisement], 201);
    }

    public function index()
    {
        $ads = Advertisement::latest()->get();
        return response()->json($ads);
    }
}
