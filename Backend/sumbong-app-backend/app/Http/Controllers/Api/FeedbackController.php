<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;

class FeedbackController extends Controller
{
    public function store(HttpRequest $request, $requestId)
    {
        $requestModel = Request::findOrFail($requestId);
        $user = $request->user();

        // Only allow users to add feedback to their own requests
        if ($requestModel->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'comment' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $feedback = Feedback::create([
            'request_id' => $requestModel->id,
            'user_id' => $user->id,
            'comment' => $validated['comment'],
            'rating' => $validated['rating'] ?? null,
        ]);

        return new FeedbackResource($feedback->load('user'));
    }

    public function index($requestId)
    {
        $requestModel = Request::findOrFail($requestId);
        $feedback = Feedback::with('user')
            ->where('request_id', $requestId)
            ->latest()
            ->get();

        return FeedbackResource::collection($feedback);
    }
}

