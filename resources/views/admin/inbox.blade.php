<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navkwa Inbox</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body class="admin-page">
  <main class="admin-shell">
    <header class="admin-topbar">
      <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
      <a href="{{ route('home') }}" class="btn btn-ghost btn-sm">View website</a>
    </header>

    <section class="admin-hero">
      <div>
        <span class="eyebrow">// Backend Inbox</span>
        <h1 class="font-display">Messages and live chat.</h1>
      </div>
      @if(session('status'))
        <p class="admin-status">{{ session('status') }}</p>
      @endif
    </section>

    <section class="admin-grid">
      <div class="admin-panel">
        <div class="admin-panel-head">
          <div>
            <h2 class="font-display">Contact Requests</h2>
            <p>{{ $contactMessages->count() }} recent request{{ $contactMessages->count() === 1 ? '' : 's' }}</p>
          </div>
        </div>

        <div class="admin-list">
          @forelse($contactMessages as $message)
            <article class="admin-message">
              <div class="admin-message-head">
                <strong>{{ $message->name }}</strong>
                <span>{{ $message->created_at->format('M j, Y g:ia') }}</span>
              </div>
              <p>{{ $message->message ?: 'No project details provided yet.' }}</p>
              <dl>
                <div><dt>Email</dt><dd>{{ $message->email }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $message->phone ?: 'Not provided' }}</dd></div>
                <div><dt>Company</dt><dd>{{ $message->company ?: 'Not provided' }}</dd></div>
                <div><dt>Country</dt><dd>{{ $message->country ?: 'Not provided' }}</dd></div>
                <div><dt>Service</dt><dd>{{ $message->service ?: 'Not selected' }}</dd></div>
                <div><dt>Budget</dt><dd>{{ $message->budget ?: 'Not selected' }}</dd></div>
                <div><dt>Timeline</dt><dd>{{ $message->timeline ?: 'Not selected' }}</dd></div>
              </dl>
              @if($message->attachment_path)
                <a class="admin-file-link" href="{{ \Illuminate\Support\Facades\Storage::url($message->attachment_path) }}" target="_blank" rel="noreferrer">Open attachment</a>
              @endif
            </article>
          @empty
            <p class="admin-empty">No contact requests received yet.</p>
          @endforelse
        </div>
      </div>

      <div class="admin-panel">
        <div class="admin-panel-head">
          <div>
            <h2 class="font-display">Live Chat</h2>
            <p>{{ $chatSessions->count() }} conversation{{ $chatSessions->count() === 1 ? '' : 's' }}</p>
          </div>
        </div>

        <div class="admin-list">
          @forelse($chatSessions as $sessionId => $messages)
            <article class="admin-message">
              <div class="admin-message-head">
                <strong class="font-mono">{{ \Illuminate\Support\Str::limit($sessionId, 18) }}</strong>
                <span>{{ $messages->first()->created_at->format('M j, Y g:ia') }}</span>
              </div>
              <div class="admin-chat-log">
                @foreach($messages->reverse() as $chat)
                  <p><span>{{ ucfirst($chat->sender) }}</span>{{ $chat->message }}</p>
                @endforeach
              </div>
            </article>
          @empty
            <p class="admin-empty">No chat messages received yet.</p>
          @endforelse
        </div>
      </div>
    </section>
  </main>
</body>
</html>
