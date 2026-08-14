<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatMessageController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string', 'max:120'],
            'after_id' => ['nullable', 'integer', 'min:0'],
        ]);

        $messages = ChatMessage::query()
            ->where('session_id', $validated['session_id'])
            ->when($validated['after_id'] ?? null, fn ($query, $afterId) => $query->where('id', '>', $afterId))
            ->oldest()
            ->get(['id', 'sender', 'message', 'created_at']);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
            'source_url' => ['nullable', 'string', 'max:2048'],
            'source_title' => ['nullable', 'string', 'max:180'],
        ]);

        $sessionId = ($validated['session_id'] ?? null) ?: (string) Str::uuid();

        $userMessage = ChatMessage::create([
            'session_id' => $sessionId,
            'sender' => 'user',
            'message' => $validated['message'],
            'source_url' => $validated['source_url'] ?? $request->headers->get('referer'),
            'source_title' => $validated['source_title'] ?? null,
        ]);

        ActivityLog::create([
            'action' => 'New chat message received',
            'module' => 'Support',
            'record_type' => ChatMessage::class,
            'record_id' => $userMessage->id,
            'new_values' => [
                'session_id' => $sessionId,
                'source_url' => $userMessage->source_url,
                'source_title' => $userMessage->source_title,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'messages' => [
                $userMessage->only(['id', 'sender', 'message', 'created_at']),
            ],
        ], 201);
    }
}
