<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contractor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Service;

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
}
