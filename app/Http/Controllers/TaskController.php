<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contractor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Task;

class TaskController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'service_id'  => 'required|exists:services,id',
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'location'    => 'required|string',
        ]);

        $task = Task::create([
            'user_id'     => Auth::id(),
            'service_id'  => $request->service_id,
            'title'       => $request->title,
            'description' => $request->description,
            'location'    => $request->location,
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
