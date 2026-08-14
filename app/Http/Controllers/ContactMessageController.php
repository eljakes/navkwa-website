<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'country' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:80'],
            'service' => ['nullable', 'string', 'max:120'],
            'industry' => ['nullable', 'string', 'max:120'],
            'budget' => ['nullable', 'string', 'max:120'],
            'timeline' => ['nullable', 'string', 'max:120'],
            'existing_system' => ['nullable', 'string', 'max:500'],
            'preferred_contact_method' => ['nullable', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $context = collect([
            'Industry' => $validated['industry'] ?? null,
            'Existing system or process' => $validated['existing_system'] ?? null,
            'Preferred contact method' => $validated['preferred_contact_method'] ?? null,
        ])->filter(fn ($value) => filled($value));

        if ($context->isNotEmpty()) {
            $validated['message'] = trim(($validated['message'] ?? '')."\n\nProject context\n".$context
                ->map(fn ($value, $label) => "{$label}: {$value}")
                ->implode("\n"));
        }

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('contact-attachments', 'public');
        }

        unset($validated['attachment'], $validated['industry'], $validated['existing_system'], $validated['preferred_contact_method']);

        $message = ContactMessage::create($validated);
        ActivityLog::create([
            'action' => 'New contact enquiry submitted',
            'module' => 'Enquiries',
            'record_type' => ContactMessage::class,
            'record_id' => $message->id,
            'new_values' => $message->only(['name', 'email', 'service', 'country']),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Your request has been received.',
            'id' => $message->id,
        ], 201);
    }
}
