<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contractor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;


class TaskController extends Controller
{
    public function create(Request $request)
{
    $request->validate([
        'service_id'   => 'required|string',
        'title'        => 'required|string',
        'description'  => 'nullable|string',
        'location'     => 'required|string',
        'casa'         => 'nullable|string',      // Adjust validation as needed
        'car_brand'    => 'nullable|string',
        'car_category' => 'nullable|string',
        'fuel_type'    => 'nullable|string',
    ]);

    $task = Task::create([
        'user_id'      => Auth::id(),
        'service_id'   => $request->service_id,
        'title'        => $request->title,
        'description'  => $request->description,
        'location'     => $request->location,
        'casa'         => $request->casa,
        'car_brand'    => $request->car_brand,
        'car_category' => $request->car_category,
        'fuel_type'    => $request->fuel_type,
    ]);

    return response()->json(['message' => 'Task created successfully', 'task' => $task]);
}


    public function myTasks()
    {
        return response()->json(Task::with('offers')->where('user_id', Auth::id())->get());
    }

    public function availableTasks()
    {
        $contractor = Contractor::where('user_id', Auth::id())->first();
        if (! $contractor) return response()->json(['error' => 'Contractor not found'], 404);

        return response()->json(Task::where('status', 'pending')
            ->whereIn('service_id', $contractor->service_categories)->get());
    }
}
