<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contractor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Service;
use App\Models\BuildingService;
use App\Models\VehicleService;
use App\Models\EmergencyService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json(Service::all());
    }
        public function buildingServices(): JsonResponse
    {
        $services = BuildingService::all();
        return response()->json($services);
    }
    public function vehicleServices(): JsonResponse
    {
        $services = VehicleService::all();
        return response()->json($services);
    }
    public function emergencyServices(): JsonResponse
    {
        $services = EmergencyService::all();
        return response()->json($services);
    }
    public function damagedScans(Request $request)
    {
        $userId = $request->user()->id;

        $services = BuildingService::whereHas('tasks', function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->whereNotNull('damaged_parts');
        })->get();

        return response()->json([
            'data' => $services
        ]);
    }
}
