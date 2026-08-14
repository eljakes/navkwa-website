<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Navkwa Admin Portal</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
  <script defer src="{{ asset('assets/js/main.js') }}"></script>
</head>
<body class="admin-page">
  @php
    $setting = fn ($key, $default = '') => old("settings.$key", $settings[$key] ?? $default);
    $chartMax = fn ($items) => max(1, collect($items)->max('value') ?: 1);
    $chartHasData = fn ($items) => collect($items)->sum('value') > 0;
    $navGroups = [
      'Dashboard' => ['dashboard' => ['label' => 'Overview', 'route' => 'admin.dashboard']],
      'Sales' => [
        'enquiries' => ['label' => 'Enquiries', 'route' => 'admin.enquiries.index'],
        'leads' => ['label' => 'Leads', 'route' => 'admin.leads.index'],
        'consultations' => ['label' => 'Consultations', 'route' => 'admin.consultations.index'],
      ],
      'Content' => ['content' => ['label' => 'Website Content', 'route' => 'admin.content.index']],
      'Support' => ['live-chats' => ['label' => 'Live Chats', 'route' => 'admin.live-chats.index']],
      'Finance' => ['payments' => ['label' => 'Payments', 'route' => 'admin.payments.index']],
      'Careers' => ['careers' => ['label' => 'Jobs & Applications', 'route' => 'admin.careers.index']],
      'Marketing' => ['marketing' => ['label' => 'Subscribers', 'route' => 'admin.marketing.index']],
      'Management' => ['management' => ['label' => 'Users & Roles', 'route' => 'admin.management.index']],
      'System' => ['system' => ['label' => 'Settings & Logs', 'route' => 'admin.system.index']],
    ];
  @endphp

  <main class="admin-app">
    <aside class="admin-sidebar">
      <div class="admin-sidebar-identity">
        <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
        <div class="admin-user-card">
          <strong>{{ \Illuminate\Support\Str::of(auth()->user()->name)->replaceStart('Navkwa ', '') }}</strong>
          <span>{{ auth()->user()->role ?? 'Administrator' }}</span>
        </div>
      </div>

      <nav class="admin-side-nav" aria-label="Admin portal navigation">
        @foreach($navGroups as $group => $items)
          <p>{{ $group }}</p>
          @foreach($items as $target => $item)
            <a href="{{ route($item['route']) }}" @class(['active' => $section === $target]) @if($section === $target) aria-current="page" @endif>{{ $item['label'] }}</a>
          @endforeach
        @endforeach
      </nav>

      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="btn btn-ghost btn-sm" type="submit">Sign Out</button>
      </form>
    </aside>

    <div class="admin-main">
      <header class="admin-topbar">
        <div class="admin-top-copy">
          <span class="eyebrow">// Operations Portal</span>
          <strong>Navkwa Admin</strong>
        </div>
        <div class="admin-top-actions">
          <form class="admin-search" method="GET" action="{{ route('admin.enquiries.index') }}">
            <input name="q" value="{{ request('q') }}" placeholder="Search enquiries">
            <button type="submit">Search</button>
          </form>
          <a href="{{ route('home') }}" class="btn btn-primary btn-sm">View Website</a>
        </div>
      </header>

      @if(session('status'))
        <p class="admin-status">{{ session('status') }}</p>
      @endif

      @if($errors->any())
        <div class="payment-alert">{{ $errors->first() }}</div>
      @endif

      @if($section === 'dashboard')
      <section class="admin-section" id="dashboard">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Dashboard Overview</h2>
            <p>Real-time summary from enquiries, leads, bookings, chat, payments, subscribers, jobs, and tracked visits.</p>
          </div>
        </div>

        <div class="admin-metric-grid">
          @foreach($metrics as $metric)
            <article class="admin-metric-card">
              <span>{{ $metric['label'] }}</span>
              <strong>{{ $metric['value'] }}</strong>
              <p>{{ $metric['hint'] }}</p>
            </article>
          @endforeach
        </div>

        <div class="admin-chart-grid">
          @foreach([
            'Enquiries received over time' => $charts['enquiriesOverTime'],
            'Website traffic trends' => $charts['trafficOverTime'],
            'Enquiries by service' => $charts['enquiriesByService'],
            'Leads by status' => $charts['leadsByStatus'],
            'Payments by status' => $charts['paymentsByStatus'],
            'Payments by product' => $charts['paymentsByProduct'],
            'Most visited pages' => $charts['mostVisitedPages'],
            'Enquiries by country' => $charts['enquiriesByCountry'],
          ] as $title => $items)
            <article class="admin-panel admin-chart-card">
              <div class="admin-panel-head">
                <div>
                  <h3 class="font-display">{{ $title }}</h3>
                </div>
              </div>
              @if($chartHasData($items))
                <div class="admin-bars">
                  @foreach($items as $point)
                    <div class="admin-bar-row">
                      <span>{{ $point['label'] }}</span>
                      <div><i style="--bar: {{ round(($point['value'] / $chartMax($items)) * 100) }}%"></i></div>
                      <b>{{ $point['value'] }}</b>
                    </div>
                  @endforeach
                </div>
              @else
                <p class="admin-empty">No data recorded yet.</p>
              @endif
            </article>
          @endforeach
        </div>

        <div class="admin-grid">
          <article class="admin-panel">
            <div class="admin-panel-head">
              <div>
                <h3 class="font-display">Tasks Requiring Attention</h3>
                <p>System alerts generated from real records and configuration.</p>
              </div>
            </div>
            <div class="admin-list compact">
              @forelse($alerts as $alert)
                <p class="admin-alert-item">{{ $alert }}</p>
              @empty
                <p class="admin-empty">No urgent alerts right now.</p>
              @endforelse
            </div>
          </article>

          <article class="admin-panel">
            <div class="admin-panel-head">
              <div>
                <h3 class="font-display">Recent Activity</h3>
                <p>Audit trail for public events and admin actions.</p>
              </div>
            </div>
            <div class="admin-timeline">
              @forelse($recentActivity as $activity)
                <p><span>{{ optional($activity->created_at)->format('M j, g:ia') }}</span>{{ $activity->action }} @if($activity->module) <b>{{ $activity->module }}</b> @endif</p>
              @empty
                <p class="admin-empty">No activity logged yet.</p>
              @endforelse
            </div>
          </article>
        </div>
      </section>
      @endif

      @if($section === 'enquiries')
      <section class="admin-section" id="enquiries">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Enquiries & Contact Messages</h2>
            <p>Search, filter, assign, mark read/unread, add notes, export, reply by email, archive, and convert qualified enquiries into leads.</p>
          </div>
          <a class="btn btn-ghost btn-sm" href="{{ route('admin.enquiries.export', request()->query()) }}">Export CSV</a>
        </div>

        <form class="admin-filter-bar" method="GET" action="{{ route('admin.enquiries.index') }}">
          <input class="field" name="q" value="{{ request('q') }}" placeholder="Search name, email, company, or message">
          <select class="field" name="status">
            <option value="">All statuses</option>
            @foreach($enquiryStatuses as $status)
              <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
          </select>
          <select class="field" name="service">
            <option value="">All services</option>
            @foreach($enquiryServices as $service)
              <option value="{{ $service }}" @selected(request('service') === $service)>{{ $service }}</option>
            @endforeach
          </select>
          <select class="field" name="country">
            <option value="">All countries</option>
            @foreach($enquiryCountries as $country)
              <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
            @endforeach
          </select>
          <input class="field" type="date" name="date_from" value="{{ request('date_from') }}">
          <input class="field" type="date" name="date_to" value="{{ request('date_to') }}">
          <button class="btn btn-primary btn-sm" type="submit">Filter</button>
        </form>

        <div class="admin-list">
          @forelse($enquiries as $message)
            <article class="admin-record-card">
              <div class="admin-record-main">
                <div>
                  <div class="admin-message-head">
                    <strong>{{ $message->name }}</strong>
                    <span>{{ $message->created_at->format('M j, Y g:ia') }}</span>
                  </div>
                  <p>{{ $message->message ?: 'No project details provided yet.' }}</p>
                  <dl>
                    <div><dt>Email</dt><dd><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd></div>
                    <div><dt>Phone</dt><dd>{{ $message->phone ?: 'Not provided' }}</dd></div>
                    <div><dt>Company</dt><dd>{{ $message->company ?: 'Not provided' }}</dd></div>
                    <div><dt>Country</dt><dd>{{ $message->country ?: 'Not provided' }}</dd></div>
                    <div><dt>Service</dt><dd>{{ $message->service ?: 'Not selected' }}</dd></div>
                    <div><dt>Budget</dt><dd>{{ $message->budget ?: 'Not selected' }}</dd></div>
                    <div><dt>Timeline</dt><dd>{{ $message->timeline ?: 'Not selected' }}</dd></div>
                    <div><dt>Status</dt><dd><span class="admin-badge">{{ $message->status }}</span></dd></div>
                  </dl>
                  @if($message->attachment_path)
                    <a class="admin-file-link" href="{{ \Illuminate\Support\Facades\Storage::url($message->attachment_path) }}" target="_blank" rel="noreferrer">Open attachment</a>
                  @endif
                </div>

                <form class="admin-record-form" method="POST" action="{{ route('admin.enquiries.update', $message) }}">
                  @csrf
                  @method('PATCH')
                  <label class="field-label">Lead status</label>
                  <select class="field" name="status">
                    @foreach($enquiryStatuses as $status)
                      <option value="{{ $status }}" @selected($message->status === $status)>{{ $status }}</option>
                    @endforeach
                  </select>
                  <label class="field-label">Assigned staff member</label>
                  <input class="field" name="assigned_to" value="{{ $message->assigned_to }}" placeholder="Team member">
                  <label class="field-label">Internal notes</label>
                  <textarea class="field" name="internal_notes" placeholder="Private CRM notes">{{ $message->internal_notes }}</textarea>
                  <label class="admin-check">
                    <input type="checkbox" name="is_read" value="1" @checked($message->is_read)>
                    <span>Mark as read</span>
                  </label>
                  <button class="btn btn-primary btn-sm" type="submit">Save Enquiry</button>
                </form>
              </div>

              <div class="admin-record-actions">
                <a class="btn btn-ghost btn-sm" href="mailto:{{ $message->email }}?subject={{ rawurlencode('Navkwa Group Ltd. enquiry') }}">Reply by Email</a>
                <form method="POST" action="{{ route('admin.enquiries.convert-lead', $message) }}">
                  @csrf
                  <input type="hidden" name="assigned_to" value="{{ $message->assigned_to }}">
                  <input type="hidden" name="notes" value="{{ $message->internal_notes }}">
                  <button class="btn btn-ghost btn-sm" type="submit">{{ $message->lead ? 'Refresh Lead' : 'Convert to Lead' }}</button>
                </form>
                <form method="POST" action="{{ route('admin.enquiries.destroy', $message) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-ghost btn-sm" type="submit">Delete Spam</button>
                </form>
              </div>
            </article>
          @empty
            <p class="admin-empty">No enquiries match the current filters.</p>
          @endforelse
        </div>

        <div class="admin-pagination">{{ $enquiries->links() }}</div>
      </section>
      @endif

      @if($section === 'leads')
      <section class="admin-section" id="leads">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Lead Management</h2>
            <p>A lightweight CRM pipeline with value, probability, follow-up dates, assignments, notes, and activity history.</p>
          </div>
        </div>

        <form class="admin-panel admin-wide-form" method="POST" action="{{ route('admin.leads.store') }}">
          @csrf
          <h3 class="font-display">Add Lead</h3>
          <div class="admin-form-grid">
            <input class="field" name="name" placeholder="Lead name" required>
            <input class="field" name="company" placeholder="Company">
            <input class="field" type="email" name="email" placeholder="Email">
            <input class="field" name="phone" placeholder="Phone">
            <input class="field" name="source" value="Website enquiry" placeholder="Lead source">
            <input class="field" name="service" placeholder="Interested service">
            <input class="field" type="number" step="0.01" name="estimated_value" placeholder="Estimated value">
            <input class="field" type="number" min="0" max="100" name="probability" value="20" placeholder="Probability">
            <select class="field" name="sales_stage">
              @foreach($leadStages as $stage)
                <option value="{{ $stage }}">{{ $stage }}</option>
              @endforeach
            </select>
            <input class="field" name="assigned_to" placeholder="Assigned team member">
            <input class="field" type="date" name="next_follow_up_date">
          </div>
          <textarea class="field" name="notes" placeholder="Notes"></textarea>
          <button class="btn btn-primary btn-sm" type="submit">Create Lead</button>
        </form>

        <div class="admin-kanban">
          @foreach($leadStages as $stage)
            <div class="admin-kanban-column">
              <div class="admin-kanban-head">
                <strong>{{ $stage }}</strong>
                <span>{{ ($leadsByStage[$stage] ?? collect())->count() }}</span>
              </div>
              @forelse(($leadsByStage[$stage] ?? collect()) as $lead)
                <article class="admin-kanban-card">
                  <strong>{{ $lead->name }}</strong>
                  <span>{{ $lead->company ?: 'No company' }}</span>
                  <p>{{ $lead->service ?: 'Service not selected' }}</p>
                  <small>Value: {{ $lead->estimated_value ? 'GHS '.number_format((float) $lead->estimated_value, 2) : 'Not set' }} | {{ $lead->probability }}%</small>
                  <form method="POST" action="{{ route('admin.leads.update', $lead) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="name" value="{{ $lead->name }}">
                    <input type="hidden" name="company" value="{{ $lead->company }}">
                    <input type="hidden" name="email" value="{{ $lead->email }}">
                    <input type="hidden" name="phone" value="{{ $lead->phone }}">
                    <input type="hidden" name="source" value="{{ $lead->source }}">
                    <input type="hidden" name="service" value="{{ $lead->service }}">
                    <input type="hidden" name="estimated_value" value="{{ $lead->estimated_value }}">
                    <input class="field" type="number" min="0" max="100" name="probability" value="{{ $lead->probability }}">
                    <select class="field" name="sales_stage">
                      @foreach($leadStages as $option)
                        <option value="{{ $option }}" @selected($lead->sales_stage === $option)>{{ $option }}</option>
                      @endforeach
                    </select>
                    <input class="field" name="assigned_to" value="{{ $lead->assigned_to }}" placeholder="Assigned">
                    <input class="field" type="date" name="next_follow_up_date" value="{{ optional($lead->next_follow_up_date)->format('Y-m-d') }}">
                    <textarea class="field" name="notes" placeholder="Notes">{{ $lead->notes }}</textarea>
                    <button class="btn btn-ghost btn-sm" type="submit">Update</button>
                  </form>
                </article>
              @empty
                <p class="admin-empty">No leads in this stage.</p>
              @endforelse
            </div>
          @endforeach
        </div>
      </section>
      @endif

      @if($section === 'consultations')
      <section class="admin-section" id="consultations">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Consultation Booking Management</h2>
            <p>Store consultation dates, meeting links, assigned consultants, statuses, and outcome notes.</p>
          </div>
        </div>

        <form class="admin-panel admin-wide-form" method="POST" action="{{ route('admin.consultations.store') }}">
          @csrf
          <h3 class="font-display">Add Consultation</h3>
          <div class="admin-form-grid">
            <input class="field" name="client_name" placeholder="Client name" required>
            <input class="field" name="company" placeholder="Company">
            <input class="field" type="email" name="email" placeholder="Email" required>
            <input class="field" name="phone" placeholder="Phone">
            <input class="field" name="service" placeholder="Selected service">
            <input class="field" type="datetime-local" name="meeting_at">
            <input class="field" name="meeting_type" value="Discovery call" placeholder="Meeting type">
            <input class="field" type="url" name="meeting_link" placeholder="Google Meet or Zoom link">
            <input class="field" name="assigned_consultant" placeholder="Assigned consultant">
            <select class="field" name="status">
              @foreach($consultationStatuses as $status)
                <option value="{{ $status }}">{{ $status }}</option>
              @endforeach
            </select>
          </div>
          <textarea class="field" name="client_notes" placeholder="Client notes"></textarea>
          <textarea class="field" name="internal_notes" placeholder="Internal outcome notes"></textarea>
          <button class="btn btn-primary btn-sm" type="submit">Save Consultation</button>
        </form>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead><tr><th>Client</th><th>Meeting</th><th>Status</th><th>Consultant</th><th>Update</th></tr></thead>
            <tbody>
              @forelse($consultations as $consultation)
                <tr>
                  <td><strong>{{ $consultation->client_name }}</strong><span>{{ $consultation->email }}</span></td>
                  <td>{{ optional($consultation->meeting_at)->format('M j, Y g:ia') ?: 'Not scheduled' }}</td>
                  <td><span class="admin-badge">{{ $consultation->status }}</span></td>
                  <td>{{ $consultation->assigned_consultant ?: 'Unassigned' }}</td>
                  <td>
                    <form method="POST" action="{{ route('admin.consultations.update', $consultation) }}">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="client_name" value="{{ $consultation->client_name }}">
                      <input type="hidden" name="company" value="{{ $consultation->company }}">
                      <input type="hidden" name="email" value="{{ $consultation->email }}">
                      <input type="hidden" name="phone" value="{{ $consultation->phone }}">
                      <input type="hidden" name="service" value="{{ $consultation->service }}">
                      <input type="hidden" name="meeting_at" value="{{ optional($consultation->meeting_at)->format('Y-m-d\\TH:i') }}">
                      <input type="hidden" name="meeting_type" value="{{ $consultation->meeting_type }}">
                      <input type="hidden" name="meeting_link" value="{{ $consultation->meeting_link }}">
                      <input type="hidden" name="client_notes" value="{{ $consultation->client_notes }}">
                      <input class="field" name="assigned_consultant" value="{{ $consultation->assigned_consultant }}" placeholder="Consultant">
                      <select class="field" name="status">
                        @foreach($consultationStatuses as $status)
                          <option value="{{ $status }}" @selected($consultation->status === $status)>{{ $status }}</option>
                        @endforeach
                      </select>
                      <textarea class="field" name="internal_notes" placeholder="Outcome notes">{{ $consultation->internal_notes }}</textarea>
                      <button class="btn btn-ghost btn-sm" type="submit">Update</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5">No consultation bookings yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
      @endif

      @if($section === 'content')
      <section class="admin-section" id="content">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Website Content Management</h2>
            <p>Create editable content records for pages, services, portfolio, blog posts, testimonials, FAQs, careers, footer content, and calls to action.</p>
          </div>
        </div>

        <form class="admin-panel admin-wide-form" method="POST" action="{{ route('admin.content.store') }}">
          @csrf
          <h3 class="font-display">Create Content Item</h3>
          <div class="admin-form-grid">
            <select class="field" name="content_type">
              @foreach($contentTypes as $type)
                <option value="{{ $type }}">{{ $type }}</option>
              @endforeach
            </select>
            <input class="field" name="title" placeholder="Title" required>
            <input class="field" name="slug" placeholder="Slug, optional">
            <select class="field" name="status">
              @foreach($contentStatuses as $status)
                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
              @endforeach
            </select>
            <input class="field" type="number" name="display_order" value="0" placeholder="Display order">
            <input class="field" type="datetime-local" name="publish_at">
            <input class="field" name="seo_title" placeholder="SEO title">
          </div>
          <textarea class="field" name="description" placeholder="Short description"></textarea>
          <textarea class="field admin-editor" name="body" placeholder="Full content, benefits, features, process, case-study details, blog article, or FAQ answer"></textarea>
          <textarea class="field" name="seo_description" placeholder="SEO description"></textarea>
          <button class="btn btn-primary btn-sm" type="submit">Save Content</button>
        </form>

        <div class="admin-card-grid">
          @forelse($contentItems as $item)
            <article class="admin-record-card">
              <div class="admin-message-head">
                <strong>{{ $item->title }}</strong>
                <span>{{ $item->content_type }} | {{ ucfirst($item->status) }}</span>
              </div>
              <p>{{ $item->description ?: 'No description yet.' }}</p>
              <form class="admin-record-form full" method="POST" action="{{ route('admin.content.update', $item) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="content_type" value="{{ $item->content_type }}">
                <input class="field" name="title" value="{{ $item->title }}" required>
                <input class="field" name="slug" value="{{ $item->slug }}" required>
                <select class="field" name="status">
                  @foreach($contentStatuses as $status)
                    <option value="{{ $status }}" @selected($item->status === $status)>{{ ucfirst($status) }}</option>
                  @endforeach
                </select>
                <input class="field" type="number" name="display_order" value="{{ $item->display_order }}">
                <input class="field" name="seo_title" value="{{ $item->seo_title }}" placeholder="SEO title">
                <textarea class="field" name="description">{{ $item->description }}</textarea>
                <textarea class="field admin-editor" name="body">{{ $item->body }}</textarea>
                <textarea class="field" name="seo_description">{{ $item->seo_description }}</textarea>
                <input type="hidden" name="publish_at" value="{{ optional($item->publish_at)->format('Y-m-d\\TH:i') }}">
                <button class="btn btn-ghost btn-sm" type="submit">Update Content</button>
              </form>
            </article>
          @empty
            <p class="admin-empty">No content items saved yet.</p>
          @endforelse
        </div>
      </section>
      @endif

      @if($section === 'live-chats')
      <section class="admin-section" id="live-chats">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Live Chats</h2>
            <p>Review website chat conversations, assign support ownership, mark sessions read, and keep internal notes for follow-up.</p>
          </div>
        </div>

        <article class="admin-panel">
          <div class="admin-panel-head">
            <div>
              <h3 class="font-display">Live Chat Sessions</h3>
              <p>{{ $chatSessions->count() }} conversation{{ $chatSessions->count() === 1 ? '' : 's' }} stored.</p>
            </div>
          </div>
          <div class="admin-list compact">
            @forelse($chatSessions as $sessionId => $messages)
              @php
                $orderedMessages = $messages->sortBy('created_at');
                $latestMessage = $orderedMessages->last();
                $sessionMeta = $messages->firstWhere('assigned_to') ?? $messages->first();
                $sourceMessage = $messages->firstWhere('source_url');
                $unreadCount = $messages->where('sender', 'user')->where('is_read', false)->count();
              @endphp
              <article class="admin-message">
                <div class="admin-message-head">
                  <strong class="font-mono">{{ \Illuminate\Support\Str::limit($sessionId, 18) }}</strong>
                  <span>{{ $latestMessage?->created_at->format('M j, g:ia') }}</span>
                </div>
                @if($unreadCount > 0)
                  <span class="admin-badge">{{ $unreadCount }} unread visitor message{{ $unreadCount === 1 ? '' : 's' }}</span>
                @endif
                @if($sourceMessage?->source_url)
                  <p class="admin-chat-source">
                    Source:
                    <a href="{{ $sourceMessage->source_url }}" target="_blank" rel="noopener">
                      {{ $sourceMessage->source_title ?: \Illuminate\Support\Str::limit($sourceMessage->source_url, 80) }}
                    </a>
                  </p>
                @endif
                <div class="admin-chat-log">
                  @foreach($orderedMessages->take(-8) as $chat)
                    <p class="{{ $chat->sender === 'support' ? 'support' : 'visitor' }}">
                      <span>{{ $chat->sender === 'support' ? 'Support' : 'Visitor' }} · {{ $chat->created_at->format('M j, g:ia') }}</span>{{ $chat->message }}
                    </p>
                  @endforeach
                </div>
                <form class="admin-record-form full admin-chat-reply-form" method="POST" action="{{ route('admin.chat.reply', $sessionId) }}">
                  @csrf
                  <label class="field-label">Reply to visitor</label>
                  <textarea class="field" name="message" placeholder="Write a real support reply that will appear in the visitor chat window." required></textarea>
                  <button class="btn btn-primary btn-sm" type="submit">Send Reply</button>
                </form>
                <form class="admin-record-form full" method="POST" action="{{ route('admin.chat.update', $sessionId) }}">
                  @csrf
                  @method('PATCH')
                  <input class="field" name="assigned_to" value="{{ $sessionMeta?->assigned_to }}" placeholder="Assigned agent">
                  <textarea class="field" name="internal_notes" placeholder="Internal chat notes">{{ $sessionMeta?->internal_notes }}</textarea>
                  <label class="admin-check"><input type="checkbox" name="is_read" value="1" @checked($messages->every(fn ($message) => $message->is_read))><span>Mark session read</span></label>
                  <button class="btn btn-ghost btn-sm" type="submit">Save Chat</button>
                </form>
              </article>
            @empty
              <p class="admin-empty">No live-chat messages yet.</p>
            @endforelse
          </div>
        </article>
      </section>
      @endif

      @if($section === 'payments')
      <section class="admin-section" id="payments">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Payments & Navkwa Build Subscriptions</h2>
            <p>Monitor real payment transactions and connect each payment to its subscription plan. Customers start new Navkwa Build payments from the product page.</p>
          </div>
        </div>

        <div class="admin-metric-grid">
          @foreach($paymentStats as $metric)
            <article class="admin-metric-card">
              <span>{{ $metric['label'] }}</span>
              <strong>{{ $metric['value'] }}</strong>
              <p>{{ $metric['hint'] }}</p>
            </article>
          @endforeach
        </div>

        <div class="admin-grid admin-grid-single">
          <article class="admin-panel">
            <div class="admin-panel-head">
              <div>
                <h3 class="font-display">Payment Transactions</h3>
                <p>{{ $payments->count() }} recent transaction{{ $payments->count() === 1 ? '' : 's' }} across website payments and Navkwa Build subscriptions.</p>
              </div>
            </div>
            <div class="admin-list compact">
              @forelse($payments as $payment)
                <article class="admin-message">
                  <div class="admin-message-head">
                    <strong>{{ $payment->reference }}</strong>
                    <span>{{ $payment->created_at->format('M j, g:ia') }}</span>
                  </div>
                  <p>{{ $payment->customer_name }} | {{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</p>
                  <dl>
                    <div><dt>Status</dt><dd>{{ ucfirst($payment->status) }}</dd></div>
                    <div><dt>Product</dt><dd>{{ $payment->productLabel() }}</dd></div>
                    <div><dt>Subscription</dt><dd>{{ $payment->subscriptionLabel() }}</dd></div>
                    <div><dt>Method</dt><dd>{{ str_replace('_', ' ', ucfirst($payment->payment_method)) }}</dd></div>
                    <div><dt>Email</dt><dd>{{ $payment->customer_email }}</dd></div>
                    <div><dt>Phone</dt><dd>{{ $payment->customer_phone ?: 'Not provided' }}</dd></div>
                    <div><dt>Paid At</dt><dd>{{ optional($payment->paid_at)->format('M j, g:ia') ?: 'Not confirmed' }}</dd></div>
                    <div><dt>Gateway Ref</dt><dd>{{ $payment->provider_reference ?: 'Not confirmed' }}</dd></div>
                  </dl>
                  <div class="admin-record-actions">
                    @if($payment->checkout_url && $payment->status === 'pending')
                      <a class="btn btn-primary btn-sm" href="{{ $payment->checkout_url }}" target="_blank" rel="noreferrer">Resume Payment</a>
                    @endif
                  </div>
                </article>
              @empty
                <p class="admin-empty">No payment transactions yet.</p>
              @endforelse
            </div>
          </article>
        </div>
      </section>
      @endif

      @if($section === 'careers')
      <section class="admin-section" id="careers">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Careers & Applications</h2>
            <p>Create job openings and track applications with recruiter assignment and status updates.</p>
          </div>
        </div>

        <div class="admin-grid">
          <form class="admin-panel admin-wide-form" method="POST" action="{{ route('admin.jobs.store') }}">
            @csrf
            <h3 class="font-display">Create Job Opening</h3>
            <input class="field" name="title" placeholder="Job title" required>
            <div class="field-row">
              <input class="field" name="department" placeholder="Department">
              <input class="field" name="location" placeholder="Location">
            </div>
            <div class="field-row">
              <input class="field" name="employment_type" placeholder="Employment type">
              <input class="field" type="date" name="application_deadline">
            </div>
            <input class="field" name="salary_range" placeholder="Salary range">
            <select class="field" name="status">
              @foreach($jobStatuses as $status)
                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
              @endforeach
            </select>
            <textarea class="field" name="description" placeholder="Description"></textarea>
            <textarea class="field" name="responsibilities" placeholder="Responsibilities"></textarea>
            <textarea class="field" name="requirements" placeholder="Requirements"></textarea>
            <button class="btn btn-primary btn-sm" type="submit">Save Job</button>
          </form>

          <form class="admin-panel admin-wide-form" method="POST" action="{{ route('admin.applications.store') }}">
            @csrf
            <h3 class="font-display">Add Application</h3>
            <select class="field" name="job_opening_id">
              <option value="">No job selected</option>
              @foreach($jobs as $job)
                <option value="{{ $job->id }}">{{ $job->title }}</option>
              @endforeach
            </select>
            <input class="field" name="candidate_name" placeholder="Candidate name" required>
            <input class="field" type="email" name="email" placeholder="Email" required>
            <input class="field" name="phone" placeholder="Phone">
            <select class="field" name="status">
              @foreach($applicationStatuses as $status)
                <option value="{{ $status }}">{{ $status }}</option>
              @endforeach
            </select>
            <input class="field" name="assigned_to" placeholder="Recruiter">
            <textarea class="field" name="notes" placeholder="Interview notes"></textarea>
            <button class="btn btn-primary btn-sm" type="submit">Save Application</button>
          </form>
        </div>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead><tr><th>Job Opening</th><th>Status</th><th>Applications</th><th>Deadline</th></tr></thead>
            <tbody>
              @forelse($jobs as $job)
                <tr>
                  <td><strong>{{ $job->title }}</strong><span>{{ $job->department ?: 'No department' }} | {{ $job->location ?: 'No location' }}</span></td>
                  <td><span class="admin-badge">{{ ucfirst($job->status) }}</span></td>
                  <td>{{ $job->applications_count }}</td>
                  <td>{{ optional($job->application_deadline)->format('M j, Y') ?: 'No deadline' }}</td>
                </tr>
              @empty
                <tr><td colspan="4">No job openings yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
      @endif

      @if($section === 'marketing')
      <section class="admin-section" id="marketing">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Newsletter & Subscribers</h2>
            <p>Manage subscriber records and export from the database later into Mailchimp, Brevo, or another email platform.</p>
          </div>
        </div>

        <form class="admin-panel admin-wide-form" method="POST" action="{{ route('admin.subscribers.store') }}">
          @csrf
          <h3 class="font-display">Add Subscriber</h3>
          <div class="admin-form-grid">
            <input class="field" name="name" placeholder="Name">
            <input class="field" type="email" name="email" placeholder="Email" required>
            <input class="field" name="source_page" placeholder="Source page">
            <select class="field" name="status">
              @foreach($subscriberStatuses as $status)
                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
              @endforeach
            </select>
          </div>
          <button class="btn btn-primary btn-sm" type="submit">Save Subscriber</button>
        </form>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead><tr><th>Subscriber</th><th>Source</th><th>Status</th><th>Subscribed</th></tr></thead>
            <tbody>
              @forelse($subscribers as $subscriber)
                <tr>
                  <td><strong>{{ $subscriber->name ?: 'No name' }}</strong><span>{{ $subscriber->email }}</span></td>
                  <td>{{ $subscriber->source_page ?: 'Manual' }}</td>
                  <td><span class="admin-badge">{{ ucfirst($subscriber->status) }}</span></td>
                  <td>{{ $subscriber->created_at->format('M j, Y') }}</td>
                </tr>
              @empty
                <tr><td colspan="4">No subscribers yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
      @endif

      @if($section === 'management')
      <section class="admin-section" id="management">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Users, Roles & Permissions</h2>
            <p>Every staff member gets an individual account. Roles are stored now and can be expanded into granular permissions as the portal grows.</p>
          </div>
        </div>

        <form class="admin-panel admin-wide-form" method="POST" action="{{ route('admin.users.store') }}">
          @csrf
          <h3 class="font-display">Create Staff User</h3>
          <div class="admin-form-grid">
            <input class="field" name="name" placeholder="Full name" required>
            <input class="field" type="email" name="email" placeholder="Email" required>
            <input class="field" name="phone" placeholder="Phone">
            <input class="field" name="job_title" placeholder="Job title">
            <input class="field" name="department" placeholder="Department">
            <select class="field" name="role">
              @foreach($userRoles as $role)
                <option value="{{ $role }}">{{ $role }}</option>
              @endforeach
            </select>
            <select class="field" name="account_status">
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
            </select>
            <input class="field" type="password" name="password" placeholder="Temporary password" required>
          </div>
          <button class="btn btn-primary btn-sm" type="submit">Create User</button>
        </form>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead><tr><th>User</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
            <tbody>
              @forelse($users as $user)
                <tr>
                  <td><strong>{{ $user->name }}</strong><span>{{ $user->email }}</span></td>
                  <td>{{ $user->role }}</td>
                  <td><span class="admin-badge">{{ ucfirst($user->account_status) }}</span></td>
                  <td>{{ optional($user->last_login_at)->format('M j, Y g:ia') ?: 'Never' }}</td>
                  <td>
                    <div class="admin-record-actions compact">
                      <a class="btn btn-ghost btn-sm" href="#user-edit-{{ $user->id }}">Edit</a>
                      @if(!auth()->user()->is($user))
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this staff user?');">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                        </form>
                      @else
                        <span class="admin-muted">Current account</span>
                      @endif
                    </div>
                  </td>
                </tr>
                <tr>
                  <td colspan="5" id="user-edit-{{ $user->id }}" class="admin-edit-cell">
                    <form class="admin-user-edit-form" method="POST" action="{{ route('admin.users.update', $user) }}">
                      @csrf
                      @method('PATCH')
                      <div class="admin-form-grid">
                        <input class="field" name="name" value="{{ $user->name }}" placeholder="Full name" required>
                        <input class="field" type="email" name="email" value="{{ $user->email }}" placeholder="Email" required>
                        <input class="field" name="phone" value="{{ $user->phone }}" placeholder="Phone">
                        <input class="field" name="job_title" value="{{ $user->job_title }}" placeholder="Job title">
                        <input class="field" name="department" value="{{ $user->department }}" placeholder="Department">
                        <select class="field" name="role">
                          @foreach($userRoles as $role)
                            <option value="{{ $role }}" @selected($user->role === $role)>{{ $role }}</option>
                          @endforeach
                        </select>
                        <select class="field" name="account_status">
                          <option value="active" @selected($user->account_status === 'active')>Active</option>
                          <option value="suspended" @selected($user->account_status === 'suspended')>Suspended</option>
                        </select>
                        <input class="field" type="password" name="password" placeholder="New password (leave blank to keep existing)">
                      </div>
                      <button class="btn btn-primary btn-sm" type="submit">Save Changes</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5">No staff users yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
      @endif

      @if($section === 'system')
      <section class="admin-section" id="system">
        <div class="admin-section-head">
          <div>
            <h2 class="font-display">Settings, Security & Audit Trail</h2>
            <p>Central place for company settings, website settings, chat settings, and audit logs.</p>
          </div>
        </div>

        <form class="admin-panel admin-wide-form" method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          @method('PATCH')
          <h3 class="font-display">System Settings</h3>
          <div class="admin-form-grid">
            <input class="field" name="settings[company_name]" value="{{ $setting('company_name', 'Navkwa Group Ltd.') }}" placeholder="Company name">
            <input class="field" type="email" name="settings[company_email]" value="{{ $setting('company_email', 'owusu-sekyere@navkwa.com') }}" placeholder="Company email">
            <input class="field" name="settings[company_phone]" value="{{ $setting('company_phone', '+233553544198') }}" placeholder="Phone">
            <input class="field" name="settings[office_address]" value="{{ $setting('office_address', 'Accra, Ghana') }}" placeholder="Office address">
            <input class="field" name="settings[business_hours]" value="{{ $setting('business_hours', 'Weekdays 8am-8pm GMT') }}" placeholder="Business hours">
            <input class="field" type="url" name="settings[website_url]" value="{{ $setting('website_url', url('/')) }}" placeholder="Website URL">
            <input class="field" name="settings[google_analytics_id]" value="{{ $setting('google_analytics_id') }}" placeholder="Google Analytics ID">
            <input class="field" name="settings[chat_provider]" value="{{ $setting('chat_provider', 'Native chat') }}" placeholder="Chat provider">
          </div>
          <textarea class="field" name="settings[notification_recipients]" placeholder="Notification recipients">{{ $setting('notification_recipients') }}</textarea>
          <label class="admin-check">
            <input type="checkbox" name="settings[maintenance_mode]" value="1" @checked($setting('maintenance_mode') === '1')>
            <span>Maintenance mode flag</span>
          </label>
          <button class="btn btn-primary btn-sm" type="submit">Save Settings</button>
        </form>

        <article class="admin-panel">
          <div class="admin-panel-head">
            <div>
              <h3 class="font-display">Activity Logs</h3>
              <p>Audit trail for important public and admin events.</p>
            </div>
          </div>
          <div class="admin-timeline">
            @forelse($recentActivity as $activity)
              <p><span>{{ optional($activity->created_at)->format('M j, Y g:ia') }}</span>{{ $activity->action }} @if($activity->user) by <b>{{ $activity->user->name }}</b> @endif @if($activity->ip_address) from {{ $activity->ip_address }} @endif</p>
            @empty
              <p class="admin-empty">No audit logs yet.</p>
            @endforelse
          </div>
        </article>
      </section>
      @endif
    </div>
  </main>
</body>
</html>
