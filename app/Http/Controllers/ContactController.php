<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactMessageMailable;

class ContactController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // 1. Validate incoming data
        $data = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|max:2000',
        ])->validate();

        // 2. Send the e-mail
        Mail::to('ziadyoutanji123@gmail.com')
            ->send(new ContactMessageMailable(
                $data['name'],
                $data['email'],
                $data['message']
            ));

        // 3. Return a JSON response
        return response()->json([
            'status'  => 'success',
            'message' => 'Message sent successfully.',
        ], 201);
    }
}
