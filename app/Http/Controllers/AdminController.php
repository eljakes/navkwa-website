<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AdminSetting;
use App\Models\ChatMessage;
use App\Models\ConsultationBooking;
use App\Models\ContactMessage;
use App\Models\ContentItem;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Lead;
use App\Models\NewsletterSubscriber;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Models\WebsiteVisit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private const ENQUIRY_STATUSES = [
        'New',
        'Contacted',
        'Qualified',
        'Proposal Sent',
        'Negotiating',
        'Won',
        'Lost',
        'Spam',
        'Archived',
    ];

    private const LEAD_STAGES = [
        'New Lead',
        'Discovery Scheduled',
        'Requirements Gathering',
        'Proposal Preparation',
        'Proposal Sent',
        'Negotiation',
        'Contract Signed',
        'Closed Won',
        'Closed Lost',
    ];

    private const CONSULTATION_STATUSES = [
        'Pending',
        'Confirmed',
        'Completed',
        'Rescheduled',
        'Cancelled',
        'No-show',
    ];

    private const CONTENT_TYPES = [
        'Homepage Section',
        'About Page',
        'Service',
        'Industry',
        'Product',
        'Portfolio Project',
        'Case Study',
        'Testimonial',
        'Team Member',
        'FAQ',
        'Blog Post',
        'Career Page',
        'Footer Content',
        'Call To Action',
    ];

    private const CONTENT_STATUSES = ['draft', 'published', 'scheduled', 'archived'];

    private const SUBSCRIBER_STATUSES = ['subscribed', 'unsubscribed', 'bounced'];

    private const JOB_STATUSES = ['active', 'inactive', 'closed'];

    private const APPLICATION_STATUSES = [
        'New',
        'Reviewing',
        'Shortlisted',
        'Interview Scheduled',
        'Interviewed',
        'Offer Sent',
        'Hired',
        'Rejected',
    ];

    private const USER_ROLES = User::ADMIN_ROLES;

    public function index(Request $request)
    {
        return $this->renderAdminPage($request, 'dashboard');
    }

    public function enquiries(Request $request)
    {
        return $this->renderAdminPage($request, 'enquiries');
    }

    public function leads(Request $request)
    {
        return $this->renderAdminPage($request, 'leads');
    }

    public function consultations(Request $request)
    {
        return $this->renderAdminPage($request, 'consultations');
    }

    public function content(Request $request)
    {
        return $this->renderAdminPage($request, 'content');
    }

    public function support()
    {
        return redirect()->route('admin.live-chats.index');
    }

    public function liveChats(Request $request)
    {
        return $this->renderAdminPage($request, 'live-chats');
    }

    public function payments(Request $request)
    {
        return $this->renderAdminPage($request, 'payments');
    }

    public function careers(Request $request)
    {
        return $this->renderAdminPage($request, 'careers');
    }

    public function marketing(Request $request)
    {
        return $this->renderAdminPage($request, 'marketing');
    }

    public function management(Request $request)
    {
        return $this->renderAdminPage($request, 'management');
    }

    public function system(Request $request)
    {
        return $this->renderAdminPage($request, 'system');
    }

    private function renderAdminPage(Request $request, string $section)
    {
        $enquiryQuery = $this->filteredEnquiries($request)->with('lead')->latest();

        return view('admin.inbox', [
            'section' => $section,
            'metrics' => $this->metrics(),
            'charts' => $this->charts(),
            'alerts' => $this->systemAlerts(),
            'recentActivity' => ActivityLog::with('user')->latest('created_at')->take(18)->get(),
            'enquiries' => $enquiryQuery->paginate(12)->withQueryString(),
            'enquiryStatuses' => self::ENQUIRY_STATUSES,
            'enquiryServices' => ContactMessage::query()->whereNotNull('service')->distinct()->orderBy('service')->pluck('service'),
            'enquiryCountries' => ContactMessage::query()->whereNotNull('country')->distinct()->orderBy('country')->pluck('country'),
            'leadStages' => self::LEAD_STAGES,
            'leads' => Lead::latest()->take(60)->get(),
            'leadsByStage' => Lead::orderBy('updated_at')->get()->groupBy('sales_stage'),
            'consultationStatuses' => self::CONSULTATION_STATUSES,
            'consultations' => ConsultationBooking::latest('meeting_at')->take(18)->get(),
            'contentTypes' => self::CONTENT_TYPES,
            'contentStatuses' => self::CONTENT_STATUSES,
            'contentItems' => ContentItem::orderBy('content_type')->orderBy('display_order')->latest()->take(24)->get(),
            'subscribers' => NewsletterSubscriber::latest()->take(18)->get(),
            'subscriberStatuses' => self::SUBSCRIBER_STATUSES,
            'jobs' => JobOpening::withCount('applications')->latest()->take(16)->get(),
            'jobStatuses' => self::JOB_STATUSES,
            'applications' => JobApplication::with('jobOpening')->latest()->take(16)->get(),
            'applicationStatuses' => self::APPLICATION_STATUSES,
            'chatSessions' => ChatMessage::latest()->get()->groupBy('session_id'),
            'payments' => PaymentTransaction::latest()->take(16)->get(),
            'paymentStats' => $this->paymentStats(),
            'users' => User::latest()->get(),
            'userRoles' => self::USER_ROLES,
            'settings' => AdminSetting::query()->pluck('value', 'key'),
        ]);
    }

    public function exportEnquiries(Request $request)
    {
        $filename = 'navkwa-enquiries-'.now()->format('Y-m-d-His').'.csv';
        $enquiries = $this->filteredEnquiries($request)->oldest();

        return Response::streamDownload(function () use ($enquiries) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Name',
                'Email',
                'Phone',
                'Company',
                'Country',
                'Service',
                'Budget',
                'Timeline',
                'Status',
                'Assigned To',
                'Read',
                'Message',
                'Attachment',
                'Internal Notes',
                'Date Submitted',
            ]);

            $enquiries->chunk(200, function ($messages) use ($handle) {
                foreach ($messages as $message) {
                    fputcsv($handle, [
                        $message->name,
                        $message->email,
                        $message->phone,
                        $message->company,
                        $message->country,
                        $message->service,
                        $message->budget,
                        $message->timeline,
                        $message->status,
                        $message->assigned_to,
                        $message->is_read ? 'Yes' : 'No',
                        $message->message,
                        $message->attachment_path,
                        $message->internal_notes,
                        optional($message->created_at)->toDateTimeString(),
                    ]);
                }
            });
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function updateEnquiry(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::ENQUIRY_STATUSES)],
            'assigned_to' => ['nullable', 'string', 'max:120'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
            'is_read' => ['nullable', 'boolean'],
        ]);

        $before = $contactMessage->only(['status', 'assigned_to', 'internal_notes', 'is_read', 'archived_at']);
        $validated['is_read'] = $request->boolean('is_read');
        $validated['archived_at'] = $validated['status'] === 'Archived' ? now() : null;
        $validated['last_contacted_at'] = $validated['status'] === 'Contacted'
            ? ($contactMessage->last_contacted_at ?: now())
            : $contactMessage->last_contacted_at;

        $contactMessage->forceFill($validated)->save();
        $this->recordActivity($request, 'Updated enquiry', 'Enquiries', $contactMessage, $before, $contactMessage->fresh()->only(array_keys($before)));

        return $this->backTo('enquiries', 'Enquiry updated.');
    }

    public function destroyEnquiry(Request $request, ContactMessage $contactMessage)
    {
        $before = $contactMessage->toArray();
        $contactMessage->delete();
        $this->recordActivity($request, 'Deleted enquiry', 'Enquiries', $contactMessage, $before, []);

        return $this->backTo('enquiries', 'Enquiry deleted.');
    }

    public function convertEnquiryToLead(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'estimated_value' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'assigned_to' => ['nullable', 'string', 'max:120'],
            'next_follow_up_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $lead = Lead::updateOrCreate(
            ['contact_message_id' => $contactMessage->id],
            [
                'name' => $contactMessage->name,
                'company' => $contactMessage->company,
                'email' => $contactMessage->email,
                'phone' => $contactMessage->phone,
                'source' => 'Website enquiry',
                'service' => $contactMessage->service,
                'estimated_value' => $validated['estimated_value'] ?? null,
                'probability' => $validated['probability'] ?? 20,
                'sales_stage' => 'New Lead',
                'assigned_to' => $validated['assigned_to'] ?? $contactMessage->assigned_to,
                'next_follow_up_date' => $validated['next_follow_up_date'] ?? null,
                'notes' => $validated['notes'] ?? $contactMessage->internal_notes,
                'activity_history' => [[
                    'at' => now()->toDateTimeString(),
                    'event' => 'Converted from enquiry #'.$contactMessage->id,
                ]],
            ],
        );

        $contactMessage->forceFill([
            'is_read' => true,
            'status' => 'Qualified',
        ])->save();

        $this->recordActivity($request, 'Converted enquiry to lead', 'Sales', $lead);

        return $this->backTo('leads', 'Lead created from enquiry.');
    }

    public function storeLead(Request $request)
    {
        $lead = Lead::create($this->validateLead($request));
        $this->recordActivity($request, 'Created lead', 'Sales', $lead);

        return $this->backTo('leads', 'Lead created.');
    }

    public function updateLead(Request $request, Lead $lead)
    {
        $before = $lead->toArray();
        $validated = $this->validateLead($request, false);

        if (($validated['sales_stage'] ?? $lead->sales_stage) !== $lead->sales_stage) {
            $history = $lead->activity_history ?: [];
            $history[] = [
                'at' => now()->toDateTimeString(),
                'event' => 'Stage changed from '.$lead->sales_stage.' to '.$validated['sales_stage'],
            ];
            $validated['activity_history'] = $history;
        }

        $lead->update($validated);
        $this->recordActivity($request, 'Updated lead', 'Sales', $lead, $before, $lead->fresh()->toArray());

        return $this->backTo('leads', 'Lead updated.');
    }

    public function storeConsultation(Request $request)
    {
        $consultation = ConsultationBooking::create($this->validateConsultation($request));
        $this->recordActivity($request, 'Created consultation booking', 'Consultations', $consultation);

        return $this->backTo('consultations', 'Consultation saved.');
    }

    public function updateConsultation(Request $request, ConsultationBooking $consultation)
    {
        $before = $consultation->toArray();
        $consultation->update($this->validateConsultation($request));
        $this->recordActivity($request, 'Updated consultation booking', 'Consultations', $consultation, $before, $consultation->fresh()->toArray());

        return $this->backTo('consultations', 'Consultation updated.');
    }

    public function storeContent(Request $request)
    {
        $content = ContentItem::create($this->validateContent($request));
        $this->recordActivity($request, 'Created content item', 'Content', $content);

        return $this->backTo('content', 'Content item saved.');
    }

    public function updateContent(Request $request, ContentItem $contentItem)
    {
        $before = $contentItem->toArray();
        $contentItem->update($this->validateContent($request, $contentItem));
        $this->recordActivity($request, 'Updated content item', 'Content', $contentItem, $before, $contentItem->fresh()->toArray());

        return $this->backTo('content', 'Content item updated.');
    }

    public function storeSubscriber(Request $request)
    {
        $validated = $this->validateSubscriber($request);
        $subscriber = NewsletterSubscriber::updateOrCreate(['email' => $validated['email']], $validated);
        $this->recordActivity($request, 'Saved subscriber', 'Marketing', $subscriber);

        return $this->backTo('marketing', 'Subscriber saved.');
    }

    public function updateSubscriber(Request $request, NewsletterSubscriber $subscriber)
    {
        $before = $subscriber->toArray();
        $validated = $this->validateSubscriber($request);
        $validated['unsubscribed_at'] = $validated['status'] === 'unsubscribed' ? now() : null;
        $subscriber->update($validated);
        $this->recordActivity($request, 'Updated subscriber', 'Marketing', $subscriber, $before, $subscriber->fresh()->toArray());

        return $this->backTo('marketing', 'Subscriber updated.');
    }

    public function storeJob(Request $request)
    {
        $job = JobOpening::create($this->validateJob($request));
        $this->recordActivity($request, 'Created job opening', 'Careers', $job);

        return $this->backTo('careers', 'Job opening saved.');
    }

    public function updateJob(Request $request, JobOpening $jobOpening)
    {
        $before = $jobOpening->toArray();
        $jobOpening->update($this->validateJob($request));
        $this->recordActivity($request, 'Updated job opening', 'Careers', $jobOpening, $before, $jobOpening->fresh()->toArray());

        return $this->backTo('careers', 'Job opening updated.');
    }

    public function storeApplication(Request $request)
    {
        $application = JobApplication::create($this->validateApplication($request));
        $this->recordActivity($request, 'Created job application', 'Careers', $application);

        return $this->backTo('careers', 'Application saved.');
    }

    public function updateApplication(Request $request, JobApplication $jobApplication)
    {
        $before = $jobApplication->toArray();
        $jobApplication->update($this->validateApplication($request));
        $this->recordActivity($request, 'Updated job application', 'Careers', $jobApplication, $before, $jobApplication->fresh()->toArray());

        return $this->backTo('careers', 'Application updated.');
    }

    public function updateChatSession(Request $request, string $sessionId)
    {
        $validated = $request->validate([
            'assigned_to' => ['nullable', 'string', 'max:120'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
            'is_read' => ['nullable', 'boolean'],
        ]);

        $validated['is_read'] = $request->boolean('is_read');

        ChatMessage::where('session_id', $sessionId)->update($validated);
        $this->recordActivity($request, 'Updated chat session', 'Support', null, [], ['session_id' => $sessionId] + $validated);

        return $this->backTo('live-chats', 'Chat session updated.');
    }

    public function storeChatReply(Request $request, string $sessionId)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        abort_unless(ChatMessage::where('session_id', $sessionId)->exists(), 404);

        $reply = ChatMessage::create([
            'session_id' => $sessionId,
            'sender' => 'support',
            'message' => $validated['message'],
            'is_read' => true,
            'assigned_to' => $request->user()?->name,
        ]);

        ChatMessage::where('session_id', $sessionId)
            ->where('sender', 'user')
            ->update(['is_read' => true]);

        $this->recordActivity($request, 'Sent live chat reply', 'Support', $reply, [], [
            'session_id' => $sessionId,
            'message_id' => $reply->id,
        ]);

        return $this->backTo('live-chats', 'Reply sent to the website chat.');
    }

    public function storeUser(Request $request)
    {
        $validated = $this->validateUser($request);
        $user = User::create($validated);
        $this->recordActivity($request, 'Created staff user', 'Management', $user);

        return $this->backTo('management', 'Staff user created.');
    }

    public function updateUser(Request $request, User $user)
    {
        $before = $user->toArray();
        $validated = $this->validateUser($request, $user);
        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }
        $user->update($validated);
        $this->recordActivity($request, 'Updated staff user', 'Management', $user, $before, $user->fresh()->toArray());

        return $this->backTo('management', 'Staff user updated.');
    }

    public function destroyUser(Request $request, User $user)
    {
        if ($request->user()?->is($user)) {
            return $this->backTo('management', 'You cannot delete your own signed-in account.');
        }

        $before = $user->toArray();
        $user->delete();

        $this->recordActivity($request, 'Deleted staff user', 'Management', null, $before);

        return redirect()->route('admin.management.index');
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'settings.company_name' => ['nullable', 'string', 'max:160'],
            'settings.company_email' => ['nullable', 'email', 'max:180'],
            'settings.company_phone' => ['nullable', 'string', 'max:80'],
            'settings.office_address' => ['nullable', 'string', 'max:240'],
            'settings.business_hours' => ['nullable', 'string', 'max:160'],
            'settings.website_url' => ['nullable', 'url', 'max:240'],
            'settings.google_analytics_id' => ['nullable', 'string', 'max:80'],
            'settings.notification_recipients' => ['nullable', 'string', 'max:500'],
            'settings.chat_provider' => ['nullable', 'string', 'max:120'],
            'settings.maintenance_mode' => ['nullable', 'boolean'],
        ]);

        foreach (($validated['settings'] ?? []) as $key => $value) {
            AdminSetting::updateOrCreate(
                ['key' => $key],
                [
                    'group' => Str::before($key, '_') ?: 'company',
                    'value' => is_bool($value) ? ($value ? '1' : '0') : $value,
                    'type' => is_bool($value) ? 'boolean' : 'text',
                ],
            );
        }

        $this->recordActivity($request, 'Updated system settings', 'System', null, [], $validated['settings'] ?? []);

        return $this->backTo('system', 'Settings updated.');
    }

    private function filteredEnquiries(Request $request)
    {
        return ContactMessage::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = '%'.$request->query('q').'%';
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('company', 'like', $search)
                        ->orWhere('message', 'like', $search);
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('service'), fn ($query) => $query->where('service', $request->query('service')))
            ->when($request->filled('country'), fn ($query) => $query->where('country', $request->query('country')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->query('date_to')));
    }

    private function metrics(): array
    {
        $totalEnquiries = ContactMessage::count();
        $websiteVisits = WebsiteVisit::count();
        $unreadEnquiries = ContactMessage::where('is_read', false)->count();
        $unreadChats = ChatMessage::where('sender', 'user')->where('is_read', false)->count();
        $paidRevenue = (float) PaymentTransaction::where('status', 'paid')->sum('amount');
        $pendingPayments = PaymentTransaction::where('status', 'pending')->count();
        $navkwaBuildPayments = PaymentTransaction::where('product', 'navkwa_build')->count();

        return [
            ['label' => 'Total Enquiries', 'value' => $totalEnquiries, 'hint' => 'All submitted contact requests'],
            ['label' => 'New Enquiries Today', 'value' => ContactMessage::whereDate('created_at', today())->count(), 'hint' => 'Needs same-day triage'],
            ['label' => 'Unread Messages', 'value' => $unreadEnquiries + $unreadChats, 'hint' => $unreadEnquiries.' enquiries, '.$unreadChats.' chats'],
            ['label' => 'Revenue Collected', 'value' => 'GH₵'.number_format($paidRevenue, 2), 'hint' => 'Confirmed paid transactions'],
            ['label' => 'Pending Payments', 'value' => $pendingPayments, 'hint' => 'Awaiting provider confirmation'],
            ['label' => 'Navkwa Build Payments', 'value' => $navkwaBuildPayments, 'hint' => 'Subscription checkouts started'],
            ['label' => 'Leads Awaiting Response', 'value' => Lead::whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])->where(function ($query) {
                $query->whereNull('next_follow_up_date')->orWhereDate('next_follow_up_date', '<=', today());
            })->count(), 'hint' => 'Follow-up due now'],
            ['label' => 'Consultations Booked', 'value' => ConsultationBooking::whereNotIn('status', ['Cancelled', 'No-show'])->count(), 'hint' => 'Active consultation records'],
            ['label' => 'Newsletter Subscribers', 'value' => NewsletterSubscriber::where('status', 'subscribed')->count(), 'hint' => 'Active audience'],
            ['label' => 'Job Applications', 'value' => JobApplication::count(), 'hint' => 'Candidates in pipeline'],
            ['label' => 'Website Visits', 'value' => $websiteVisits, 'hint' => 'Tracked page views'],
            ['label' => 'Conversion Rate', 'value' => $websiteVisits > 0 ? round(($totalEnquiries / $websiteVisits) * 100, 1).'%' : '0%', 'hint' => 'Enquiries / visits'],
            ['label' => 'Qualified Leads', 'value' => Lead::whereIn('sales_stage', ['Proposal Preparation', 'Proposal Sent', 'Negotiation', 'Contract Signed', 'Closed Won'])->count(), 'hint' => 'Pipeline quality indicator'],
        ];
    }

    private function charts(): array
    {
        return [
            'enquiriesOverTime' => $this->dailySeries(ContactMessage::class, 'created_at'),
            'trafficOverTime' => $this->dailySeries(WebsiteVisit::class, 'visited_at'),
            'enquiriesByService' => $this->groupedCounts(ContactMessage::class, 'service', 'Not selected'),
            'leadsByStatus' => $this->groupedCounts(Lead::class, 'sales_stage', 'No stage'),
            'paymentsByStatus' => $this->groupedCounts(PaymentTransaction::class, 'status', 'Unknown status'),
            'paymentsByProduct' => $this->groupedCounts(PaymentTransaction::class, 'product', 'General payment'),
            'mostVisitedPages' => $this->groupedCounts(WebsiteVisit::class, 'path', 'Unknown page'),
            'enquiriesByCountry' => $this->groupedCounts(ContactMessage::class, 'country', 'Not provided'),
        ];
    }

    private function paymentStats(): array
    {
        $paidRevenue = (float) PaymentTransaction::where('status', 'paid')->sum('amount');
        $navkwaBuildRevenue = (float) PaymentTransaction::where('product', 'navkwa_build')->where('status', 'paid')->sum('amount');

        return [
            ['label' => 'Revenue Collected', 'value' => 'GH₵'.number_format($paidRevenue, 2), 'hint' => 'All confirmed paid transactions'],
            ['label' => 'Navkwa Build Revenue', 'value' => 'GH₵'.number_format($navkwaBuildRevenue, 2), 'hint' => 'Confirmed subscription revenue'],
            ['label' => 'Subscription Checkouts', 'value' => PaymentTransaction::where('product', 'navkwa_build')->count(), 'hint' => 'Navkwa Build payment attempts'],
            ['label' => 'Pending Payments', 'value' => PaymentTransaction::where('status', 'pending')->count(), 'hint' => 'Awaiting confirmation'],
            ['label' => 'Failed Payments', 'value' => PaymentTransaction::where('status', 'failed')->count(), 'hint' => 'Requires follow-up'],
        ];
    }

    private function dailySeries(string $model, string $column): array
    {
        return collect(range(6, 0))->map(function (int $daysAgo) use ($model, $column) {
            $date = today()->subDays($daysAgo);

            return [
                'label' => $date->format('M j'),
                'value' => $model::whereDate($column, $date)->count(),
            ];
        })->all();
    }

    private function groupedCounts(string $model, string $column, string $fallback): array
    {
        return $model::query()
            ->get([$column])
            ->map(fn ($record) => filled($record->{$column}) ? $record->{$column} : $fallback)
            ->countBy()
            ->sortDesc()
            ->take(6)
            ->map(fn ($value, $label) => ['label' => $label, 'value' => $value])
            ->values()
            ->all();
    }

    private function systemAlerts()
    {
        $alerts = collect();

        $overdueLeads = Lead::whereDate('next_follow_up_date', '<', today())
            ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
            ->count();
        if ($overdueLeads > 0) {
            $alerts->push($overdueLeads.' lead follow-up'.($overdueLeads === 1 ? ' is' : 's are').' overdue.');
        }

        $pendingConsultations = ConsultationBooking::where('status', 'Pending')->count();
        if ($pendingConsultations > 0) {
            $alerts->push($pendingConsultations.' consultation booking'.($pendingConsultations === 1 ? ' needs' : 's need').' confirmation.');
        }

        $unread = ContactMessage::where('is_read', false)->count();
        if ($unread > 0) {
            $alerts->push($unread.' enquiry message'.($unread === 1 ? ' is' : 's are').' unread.');
        }

        $paystackReady = filled(config('services.paystack.secret_key'));
        $hubtelReady = filled(config('services.hubtel.account_number'))
            && filled(config('services.hubtel.client_id'))
            && filled(config('services.hubtel.client_secret'))
            && filled(config('services.hubtel.checkout_endpoint'));

        if (! $paystackReady) {
            $alerts->push('Paystack live credentials are not configured.');
        }

        if (! $hubtelReady) {
            $alerts->push('Hubtel checkout credentials are not configured.');
        }

        return $alerts;
    }

    private function validateLead(Request $request, bool $requireName = true): array
    {
        return $request->validate([
            'name' => [$requireName ? 'required' : 'sometimes', 'string', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:80'],
            'source' => ['nullable', 'string', 'max:120'],
            'service' => ['nullable', 'string', 'max:120'],
            'estimated_value' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'probability' => ['required', 'integer', 'min:0', 'max:100'],
            'sales_stage' => ['required', Rule::in(self::LEAD_STAGES)],
            'assigned_to' => ['nullable', 'string', 'max:120'],
            'next_follow_up_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function validateConsultation(Request $request): array
    {
        return $request->validate([
            'client_name' => ['required', 'string', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:80'],
            'service' => ['nullable', 'string', 'max:120'],
            'meeting_at' => ['nullable', 'date'],
            'meeting_type' => ['nullable', 'string', 'max:120'],
            'meeting_link' => ['nullable', 'url', 'max:240'],
            'assigned_consultant' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(self::CONSULTATION_STATUSES)],
            'client_notes' => ['nullable', 'string', 'max:5000'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function validateContent(Request $request, ?ContentItem $contentItem = null): array
    {
        $validated = $request->validate([
            'content_type' => ['required', Rule::in(self::CONTENT_TYPES)],
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('content_items', 'slug')->ignore($contentItem)],
            'description' => ['nullable', 'string', 'max:2000'],
            'body' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:180'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(self::CONTENT_STATUSES)],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'publish_at' => ['nullable', 'date'],
        ]);

        $validated['slug'] = ($validated['slug'] ?? null) ?: Str::slug($validated['title']);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        return $validated;
    }

    private function validateSubscriber(Request $request): array
    {
        return $request->validate([
            'name' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:180'],
            'source_page' => ['nullable', 'string', 'max:160'],
            'status' => ['required', Rule::in(self::SUBSCRIBER_STATUSES)],
        ]);
    }

    private function validateJob(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'department' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'employment_type' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'salary_range' => ['nullable', 'string', 'max:120'],
            'application_deadline' => ['nullable', 'date'],
            'status' => ['required', Rule::in(self::JOB_STATUSES)],
        ]);
    }

    private function validateApplication(Request $request): array
    {
        return $request->validate([
            'job_opening_id' => ['nullable', 'exists:job_openings,id'],
            'candidate_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:80'],
            'status' => ['required', Rule::in(self::APPLICATION_STATUSES)],
            'assigned_to' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:80'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'role' => ['required', Rule::in(self::USER_ROLES)],
            'account_status' => ['required', Rule::in(['active', 'suspended'])],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'max:200'],
        ]);
    }

    private function backTo(string $section, string $status)
    {
        $routes = [
            'dashboard' => 'admin.dashboard',
            'enquiries' => 'admin.enquiries.index',
            'leads' => 'admin.leads.index',
            'consultations' => 'admin.consultations.index',
            'content' => 'admin.content.index',
            'live-chats' => 'admin.live-chats.index',
            'payments' => 'admin.payments.index',
            'careers' => 'admin.careers.index',
            'marketing' => 'admin.marketing.index',
            'management' => 'admin.management.index',
            'system' => 'admin.system.index',
        ];

        return redirect()->route($routes[$section] ?? 'admin.dashboard')->with('status', $status);
    }

    private function recordActivity(
        Request $request,
        string $action,
        ?string $module = null,
        ?Model $record = null,
        array $previous = [],
        array $new = [],
    ): void {
        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'module' => $module,
            'record_type' => $record ? get_class($record) : null,
            'record_id' => $record?->getKey(),
            'previous_values' => $previous ?: null,
            'new_values' => $new ?: null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
