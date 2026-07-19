<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatMessageController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string', 'max:120'],
        ]);

        $messages = ChatMessage::query()
            ->where('session_id', $validated['session_id'])
            ->oldest()
            ->get(['sender', 'message', 'created_at']);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $sessionId = ($validated['session_id'] ?? null) ?: (string) Str::uuid();

        $userMessage = ChatMessage::create([
            'session_id' => $sessionId,
            'sender' => 'user',
            'message' => $validated['message'],
        ]);

        $reply = ChatMessage::create([
            'session_id' => $sessionId,
            'sender' => 'support',
            'message' => 'Thanks. Your message has been received by Navkwa. Please leave your email or phone number if you want the team to follow up directly.',
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'messages' => [
                $userMessage->only(['sender', 'message', 'created_at']),
                $reply->only(['sender', 'message', 'created_at']),
            ],
        ], 201);
    }
}
