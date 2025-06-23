<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contractor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class OfferController extends Controller
{
    public function makeOffer(Request $request, $taskId)
    {
        $request->validate([
            'price'    => 'required|numeric',
            'comments' => 'nullable|string',
        ]);

        $contractor = Contractor::where('user_id', Auth::id())->first();
        if (! $contractor) return response()->json(['error' => 'Contractor not found'], 404);

        $offer = Offer::create([
            'task_id'      => $taskId,
            'contractor_id'=> $contractor->id,
            'price'        => $request->price,
            'comments'     => $request->comments,
        ]);

        Task::where('id', $taskId)->update(['status' => 'offer_sent']);

        return response()->json(['message' => 'Offer submitted', 'offer' => $offer]);
    }

    public function respondToOffer(Request $request, $offerId)
    {
        $request->validate(['response' => 'required|in:accepted,declined']);

        $offer = Offer::findOrFail($offerId);
        $task  = $offer->task;

        if ($task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $offer->status = $request->response;
        $offer->save();

        $task->status = $request->response;
        $task->save();

        return response()->json(['message' => 'Response submitted']);
    }
}
