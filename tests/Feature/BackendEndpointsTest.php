<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\ChatMessage;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class BackendEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_is_available_for_monitoring(): void
    {
        $this->getJson(route('health'))
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'service' => 'navkwa-website',
            ]);
    }

    public function test_contact_messages_are_saved(): void
    {
        $response = $this->postJson(route('contact.store'), [
            'name' => 'Ama Owusu',
            'company' => 'Navkwa Client Ltd.',
            'country' => 'Ghana',
            'email' => 'ama@example.com',
            'phone' => '+233 000 000 000',
            'service' => 'Enterprise Software',
            'budget' => '$5,000 - $20,000',
            'timeline' => '1-3 months',
            'message' => 'We need a custom operations system.',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'ama@example.com',
            'service' => 'Enterprise Software',
        ]);
    }

    public function test_public_website_exposes_payment_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Payments')
            ->assertSee(route('payments.create'), false);

        $this->get(route('products.navkwa-build'))
            ->assertOk()
            ->assertSee('Payments')
            ->assertSee(route('payments.create'), false);
    }

    public function test_main_navigation_links_have_standalone_pages(): void
    {
        $routes = [
            'services.index' => 'From product strategy to production software.',
            'products.index' => 'Available and active products.',
            'industries.index' => 'Software shaped by how each industry operates.',
            'work.index' => 'Active products and internal systems.',
            'about.index' => 'A Ghanaian technology company building practical systems for African businesses.',
            'contact.index' => 'Book a 30-minute discovery conversation.',
        ];

        $home = $this->get(route('home'))->assertOk();

        foreach ($routes as $routeName => $headline) {
            $home->assertSee(route($routeName), false);

            $this->get(route($routeName))
                ->assertOk()
                ->assertSee($headline)
                ->assertSee('data-reveal', false)
                ->assertSee(route('payments.create'), false);
        }

        foreach (array_diff(array_keys($routes), ['about.index']) as $routeName) {
            $this->get(route($routeName))
                ->assertDontSee('brand-page-hero', false)
                ->assertDontSee('page-hero', false)
                ->assertDontSee('about-hero', false);
        }

        $home
            ->assertDontSee('href="#services"', false)
            ->assertDontSee('href="#products"', false)
            ->assertDontSee('href="#industries"', false)
            ->assertDontSee('href="#work"', false)
            ->assertDontSee('href="#about"', false)
            ->assertDontSee('href="#contact"', false);
    }

    public function test_homepage_is_streamlined_for_standalone_page_navigation(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Building intelligent software for', false)
            ->assertSee('Practical software systems for companies that need control, clarity, and scale.')
            ->assertSee('Choose where you want to go next.')
            ->assertSee(route('services.index'), false)
            ->assertSee(route('products.index'), false)
            ->assertSee(route('industries.index'), false)
            ->assertSee(route('work.index'), false)
            ->assertSee(route('about.index'), false)
            ->assertSee(route('contact.index'), false)
            ->assertDontSee('Questions before we talk.')
            ->assertDontSee('How much does custom software cost?')
            ->assertDontSee('Book a 30-minute discovery call to discuss your business problem');
    }

    public function test_chat_messages_are_saved_without_a_fake_reply(): void
    {
        $response = $this->postJson(route('chat.messages.store'), [
            'message' => 'Can someone contact me about an ERP build?',
            'source_url' => 'https://navkwa.test/products/navkwa-build',
            'source_title' => 'Navkwa Build - Construction Operating System',
        ]);

        $response
            ->assertCreated()
            ->assertJsonCount(1, 'messages')
            ->assertJsonPath('messages.0.sender', 'user');

        $this->assertDatabaseHas('chat_messages', [
            'sender' => 'user',
            'message' => 'Can someone contact me about an ERP build?',
            'source_url' => 'https://navkwa.test/products/navkwa-build',
            'source_title' => 'Navkwa Build - Construction Operating System',
        ]);

        $this->assertDatabaseMissing('chat_messages', [
            'sender' => 'support',
            'message' => 'Thanks. Your message has been received by Navkwa. Please leave your email or phone number if you want the team to follow up directly.',
        ]);
    }

    public function test_admin_can_reply_to_live_chat_and_visitor_can_fetch_it(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);

        $visitorMessage = ChatMessage::create([
            'session_id' => 'navkwa-build-session',
            'sender' => 'user',
            'message' => 'I need pricing for Navkwa Build.',
            'source_url' => 'https://navkwa.test/products/navkwa-build',
            'source_title' => 'Navkwa Build - Construction Operating System',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.live-chats.index'))
            ->assertOk()
            ->assertSee('Live Chat Sessions')
            ->assertSee('Navkwa Build - Construction Operating System');

        $this->actingAs($admin)
            ->post(route('admin.chat.reply', 'navkwa-build-session'), [
                'message' => 'Thanks for reaching out. We can walk you through the Professional plan today.',
            ])
            ->assertRedirect(route('admin.live-chats.index'));

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => 'navkwa-build-session',
            'sender' => 'support',
            'message' => 'Thanks for reaching out. We can walk you through the Professional plan today.',
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'id' => $visitorMessage->id,
            'is_read' => true,
        ]);

        $this->getJson(route('chat.messages.index', [
            'session_id' => 'navkwa-build-session',
            'after_id' => $visitorMessage->id,
        ]))
            ->assertOk()
            ->assertJsonCount(1, 'messages')
            ->assertJsonPath('messages.0.sender', 'support')
            ->assertJsonPath('messages.0.message', 'Thanks for reaching out. We can walk you through the Professional plan today.');
    }

    public function test_payment_form_loads(): void
    {
        $this->get(route('payments.create'))
            ->assertOk()
            ->assertSee('MTN MoMo')
            ->assertSee('Visa')
            ->assertSee('Home')
            ->assertDontSee('Contact')
            ->assertDontSee('Gateway')
            ->assertDontSee('Available rails')
            ->assertDontSee('Debit Cards')
            ->assertDontSee('Website')
            ->assertDontSee('Payment provider')
            ->assertDontSee('Paystack')
            ->assertDontSee('Hubtel')
            ->assertSee('Card number')
            ->assertSee('Expiry')
            ->assertSee('CVV')
            ->assertSee('Card details are entered on the next secure payment screen')
            ->assertDontSee('Demo');
    }

    public function test_payment_initialize_fails_without_live_provider_credentials(): void
    {
        config(['services.paystack.secret_key' => null]);

        $response = $this->post(route('payments.initialize'), [
            'payment_method' => 'mobile_money',
            'mobile_network' => 'mtn_momo',
            'amount' => '125.50',
            'customer_name' => 'Ama Owusu',
            'customer_email' => 'ama@example.com',
            'customer_phone' => '+233 000 000 000',
            'description' => 'Discovery deposit',
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseHas('payment_transactions', [
            'provider' => 'paystack',
            'payment_method' => 'mobile_money',
            'mobile_network' => 'mtn_momo',
            'amount' => '125.50',
            'status' => 'failed',
        ]);
    }

    public function test_paystack_initialize_redirects_to_live_checkout(): void
    {
        config([
            'services.paystack.secret_key' => 'sk_test_real_key',
            'services.paystack.base_url' => 'https://api.paystack.co',
        ]);

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test-reference',
                    'reference' => 'paystack-reference',
                ],
            ]),
        ]);

        $response = $this->post(route('payments.initialize'), [
            'provider' => 'paystack',
            'payment_method' => 'card',
            'amount' => '125.50',
            'customer_name' => 'Ama Owusu',
            'customer_email' => 'ama@example.com',
            'customer_phone' => '+233 000 000 000',
            'description' => 'Discovery deposit',
        ]);

        $response->assertRedirect('https://checkout.paystack.com/test-reference');

        $this->assertDatabaseHas('payment_transactions', [
            'provider' => 'paystack',
            'payment_method' => 'card',
            'amount' => '125.50',
            'status' => 'pending',
            'checkout_url' => 'https://checkout.paystack.com/test-reference',
            'provider_reference' => 'paystack-reference',
        ]);
    }

    public function test_hubtel_initialize_redirects_to_live_checkout(): void
    {
        config([
            'services.hubtel.account_number' => '123456',
            'services.hubtel.client_id' => 'hubtel-client',
            'services.hubtel.client_secret' => 'hubtel-secret',
            'services.hubtel.checkout_endpoint' => 'https://hubtel.test/checkout',
        ]);

        Http::fake([
            'hubtel.test/checkout' => Http::response([
                'ResponseCode' => '0000',
                'Data' => [
                    'CheckoutUrl' => 'https://checkout.hubtel.com/test-reference',
                    'CheckoutId' => 'hubtel-checkout-id',
                ],
            ]),
        ]);

        $response = $this->post(route('payments.initialize'), [
            'provider' => 'hubtel',
            'payment_method' => 'mobile_money',
            'mobile_network' => 'mtn_momo',
            'amount' => '250.00',
            'customer_name' => 'Ama Owusu',
            'customer_email' => 'ama@example.com',
            'customer_phone' => '+233 000 000 000',
            'description' => 'Invoice payment',
        ]);

        $response->assertRedirect('https://checkout.hubtel.com/test-reference');

        $this->assertDatabaseHas('payment_transactions', [
            'provider' => 'hubtel',
            'payment_method' => 'mobile_money',
            'amount' => '250.00',
            'status' => 'pending',
            'checkout_url' => 'https://checkout.hubtel.com/test-reference',
            'provider_reference' => 'hubtel-checkout-id',
        ]);
    }

    public function test_navkwa_build_subscription_checkout_uses_server_pricing(): void
    {
        config([
            'services.paystack.secret_key' => 'sk_test_real_key',
            'services.paystack.base_url' => 'https://api.paystack.co',
            'navkwa_build.annual_billable_months' => 10,
        ]);

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/navkwa-build',
                    'reference' => 'navkwa-build-paystack-reference',
                ],
            ]),
        ]);

        $response = $this->post(route('payments.initialize'), [
            'provider' => 'paystack',
            'product' => 'navkwa_build',
            'plan' => 'professional',
            'billing_cycle' => 'annual',
            'payment_method' => 'mobile_money',
            'mobile_network' => 'mtn_momo',
            'amount' => '1.00',
            'customer_name' => 'Owusu Construction Ltd.',
            'customer_email' => 'billing@example.com',
            'customer_phone' => '+233 000 000 000',
        ]);

        $response->assertRedirect('https://checkout.paystack.com/navkwa-build');

        $this->assertDatabaseHas('payment_transactions', [
            'product' => 'navkwa_build',
            'plan' => 'professional',
            'billing_cycle' => 'annual',
            'amount' => '9990.00',
            'description' => 'Navkwa Build Professional annual subscription',
        ]);
    }

    public function test_signed_paystack_webhook_marks_payment_paid_and_dashboard_reflects_it(): void
    {
        config(['services.paystack.secret_key' => 'sk_test_real_key']);

        $payment = PaymentTransaction::create([
            'reference' => 'NVK-TEST-001',
            'provider' => 'paystack',
            'product' => 'navkwa_build',
            'plan' => 'professional',
            'billing_cycle' => 'monthly',
            'payment_method' => 'card',
            'amount' => '999.00',
            'currency' => 'GHS',
            'customer_name' => 'Owusu Construction Ltd.',
            'customer_email' => 'billing@example.com',
            'status' => 'pending',
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => $payment->reference,
                'status' => 'success',
                'amount' => 99900,
                'currency' => 'GHS',
            ],
        ];
        $body = json_encode($payload);

        $this->postJson(route('payments.paystack.webhook'), $payload, [
            'x-paystack-signature' => hash_hmac('sha512', $body, 'sk_test_real_key'),
        ])->assertOk();

        $this->assertDatabaseHas('payment_transactions', [
            'reference' => $payment->reference,
            'status' => 'paid',
        ]);

        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Revenue Collected')
            ->assertSee('GH₵999.00')
            ->assertSee('Navkwa Build Payments')
            ->assertDontSee('NVK-TEST-001');

        $this->actingAs($admin)
            ->get(route('admin.payments.index'))
            ->assertOk()
            ->assertSee('Payments & Navkwa Build Subscriptions', false)
            ->assertSee('Customers start new Navkwa Build payments from the product page.')
            ->assertSee('NVK-TEST-001')
            ->assertSee('Navkwa Build subscription')
            ->assertSee('Professional (Monthly)')
            ->assertDontSee('New Navkwa Build Payment')
            ->assertDontSee(route('products.navkwa-build.payment', ['plan' => 'professional', 'billing_cycle' => 'monthly']), false);
    }

    public function test_navkwa_build_plan_buttons_open_payment_details_page(): void
    {
        $this->get(route('products.navkwa-build'))
            ->assertOk()
            ->assertSee('Start Navkwa Build payment')
            ->assertSee('Start Payment')
            ->assertSee(route('products.navkwa-build.payment', ['plan' => 'essential']), false)
            ->assertSee(route('products.navkwa-build.payment', ['plan' => 'professional']), false)
            ->assertSee(route('products.navkwa-build.payment', ['plan' => 'business']), false)
            ->assertDontSee('data-build-checkout-form', false)
            ->assertDontSee('Continue to Live Checkout');
    }

    public function test_navkwa_build_payment_details_page_contains_real_subscription_checkout(): void
    {
        $this->get(route('products.navkwa-build.payment', ['plan' => 'business']))
            ->assertOk()
            ->assertSee(route('payments.initialize'), false)
            ->assertSee('Complete your Navkwa Build payment details')
            ->assertSee('Business - GHS 2,499.00/month')
            ->assertSee('Visa / Mastercard')
            ->assertSee('Card number')
            ->assertSee('Expiry')
            ->assertSee('CVV')
            ->assertSee('Card details are entered on the next secure payment screen')
            ->assertDontSee('Paystack')
            ->assertDontSee('Hubtel')
            ->assertDontSee('Payment provider')
            ->assertDontSee('Navkwa does not collect or store card details')
            ->assertDontSee('Backend-priced plan amount')
            ->assertDontSee('Dashboard payment tracking')
            ->assertDontSee('What happens next')
            ->assertDontSee('Request Quote')
            ->assertDontSee('demo checkout');
    }

    public function test_admin_portal_requires_authentication(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_sections_require_authentication(): void
    {
        $protectedRoutes = [
            'admin.enquiries.index',
            'admin.leads.index',
            'admin.consultations.index',
            'admin.content.index',
            'admin.live-chats.index',
            'admin.payments.index',
            'admin.careers.index',
            'admin.marketing.index',
            'admin.management.index',
            'admin.system.index',
        ];

        foreach ($protectedRoutes as $routeName) {
            $this->get(route($routeName))
                ->assertRedirect(route('admin.login'));
        }
    }

    public function test_admin_login_has_password_visibility_toggle(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Dashboard Login')
            ->assertSee('Sign In')
            ->assertSee('data-password-toggle', false)
            ->assertSee('aria-label="Show password"', false)
            ->assertDontSee('// Admin Portal')
            ->assertDontSee('Secure operations access for authorised staff.')
            ->assertDontSee('// Staff Sign In')
            ->assertDontSee('Remember this device')
            ->assertDontSee('Individual staff accounts only.')
            ->assertDontSee('Access Layer');
    }

    public function test_admin_login_page_discourages_password_autofill_and_browser_cache(): void
    {
        $response = $this->get(route('admin.login'));

        $response
            ->assertOk()
            ->assertSee('autocomplete="off"', false)
            ->assertSee('autocomplete="new-password"', false)
            ->assertSee('data-sensitive-password', false)
            ->assertSee('pageshow', false)
            ->assertSee('visibilitychange', false)
            ->assertSee('value=""', false)
            ->assertDontSee('autocomplete="current-password"', false)
            ->assertDontSee('Remember this device');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame('no-cache', $response->headers->get('Pragma'));
        $this->assertSame('0', $response->headers->get('Expires'));
    }

    public function test_non_staff_session_can_still_view_admin_login_page(): void
    {
        $user = User::factory()->create([
            'role' => 'Client',
            'account_status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Dashboard Login');
    }

    public function test_staff_session_is_sent_from_admin_login_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_login_rejects_non_staff_accounts(): void
    {
        $user = User::factory()->create([
            'role' => 'Client',
            'account_status' => 'active',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_login_throttles_repeated_failed_attempts(): void
    {
        $email = 'locked-admin@example.com';
        RateLimiter::clear('admin-login|'.$email.'|127.0.0.1');

        foreach (range(1, 5) as $attempt) {
            $this->from(route('admin.login'))
                ->post(route('admin.login.store'), [
                    'email' => $email,
                    'password' => 'wrong-password',
                ])
                ->assertRedirect(route('admin.login'))
                ->assertSessionHasErrors('email');
        }

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'email' => $email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertStringContainsString('Too many sign-in attempts', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_admin_login_allows_active_staff_accounts(): void
    {
        $admin = User::factory()->create([
            'role' => 'Administrator',
            'account_status' => 'active',
        ]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'email' => $admin->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_authenticated_non_staff_user_cannot_open_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'Client',
            'account_status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_dashboard_loads_for_staff_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Operations Portal')
            ->assertDontSee('Navkwa admin dashboard')
            ->assertDontSee('Backed by database records.')
            ->assertSee('Dashboard Overview')
            ->assertDontSee('Enquiries & Contact Messages')
            ->assertDontSee('Website Content Management');
    }

    public function test_admin_sidebar_items_have_dedicated_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);

        $pages = [
            'admin.enquiries.index' => 'Enquiries & Contact Messages',
            'admin.leads.index' => 'Lead Management',
            'admin.consultations.index' => 'Consultation Booking Management',
            'admin.content.index' => 'Website Content Management',
            'admin.live-chats.index' => 'Live Chats',
            'admin.payments.index' => 'Payments & Navkwa Build Subscriptions',
            'admin.careers.index' => 'Careers & Applications',
            'admin.marketing.index' => 'Newsletter & Subscribers',
            'admin.management.index' => 'Users, Roles & Permissions',
            'admin.system.index' => 'Settings, Security & Audit Trail',
        ];

        foreach ($pages as $routeName => $heading) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($heading, false)
                ->assertSee('aria-current="page"', false)
                ->assertDontSee('Dashboard Overview');
        }
    }

    public function test_old_admin_support_page_redirects_to_live_chats(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.support.index'))
            ->assertRedirect(route('admin.live-chats.index'));
    }

    public function test_admin_can_edit_and_delete_staff_users(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);
        $staff = User::factory()->create([
            'name' => 'Support Agent',
            'email' => 'support.agent@example.com',
            'role' => 'Support Agent',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.management.index'))
            ->assertOk()
            ->assertSee(route('admin.users.update', $staff), false)
            ->assertSee(route('admin.users.destroy', $staff), false)
            ->assertSee('Save Changes')
            ->assertSee('Delete');

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $staff), [
                'name' => 'Content Lead',
                'email' => 'content.lead@example.com',
                'phone' => '+233 111 222 333',
                'job_title' => 'Content Operations Lead',
                'department' => 'Content',
                'role' => 'Content Manager',
                'account_status' => 'suspended',
                'password' => '',
            ])
            ->assertRedirect(route('admin.management.index'));

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'name' => 'Content Lead',
            'email' => 'content.lead@example.com',
            'role' => 'Content Manager',
            'account_status' => 'suspended',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $staff))
            ->assertRedirect(route('admin.management.index'))
            ->assertSessionMissing('status');

        $this->assertDatabaseMissing('users', [
            'id' => $staff->id,
        ]);
    }

    public function test_admin_dashboard_intro_tagline_is_removed(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Manage enquiries, payments, live chats, content, and team operations.');
    }

    public function test_admin_can_update_and_export_enquiries(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);
        $message = ContactMessage::create([
            'name' => 'Kojo Mensah',
            'email' => 'kojo@example.com',
            'service' => 'Web App',
            'message' => 'We need a client portal.',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.enquiries.update', $message), [
                'status' => 'Qualified',
                'assigned_to' => 'Elvis',
                'internal_notes' => 'Ready for discovery call.',
                'is_read' => '1',
            ])
            ->assertRedirect(route('admin.enquiries.index'));

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'Qualified',
            'assigned_to' => 'Elvis',
            'is_read' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.enquiries.export'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_convert_enquiry_to_lead(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);
        $message = ContactMessage::create([
            'name' => 'Ama Owusu',
            'company' => 'Owusu Ltd.',
            'email' => 'ama@example.com',
            'phone' => '+233 000 111 222',
            'service' => 'Enterprise Software',
            'message' => 'We need operations software.',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.enquiries.convert-lead', $message), [
                'estimated_value' => '45000',
                'probability' => 35,
                'assigned_to' => 'Sales Manager',
                'next_follow_up_date' => now()->addWeek()->format('Y-m-d'),
                'notes' => 'Qualified buyer.',
            ])
            ->assertRedirect(route('admin.leads.index'));

        $this->assertDatabaseHas('leads', [
            'contact_message_id' => $message->id,
            'email' => 'ama@example.com',
            'sales_stage' => 'New Lead',
            'assigned_to' => 'Sales Manager',
        ]);

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'Qualified',
            'is_read' => true,
        ]);
    }

    public function test_admin_operations_modules_store_real_records(): void
    {
        $admin = User::factory()->create([
            'role' => 'Super Admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.consultations.store'), [
                'client_name' => 'Akua Boateng',
                'email' => 'akua@example.com',
                'status' => 'Pending',
                'meeting_type' => 'Discovery call',
                'meeting_link' => 'https://meet.google.com/example',
            ])
            ->assertRedirect(route('admin.consultations.index'));

        $this->actingAs($admin)
            ->post(route('admin.content.store'), [
                'content_type' => 'Service',
                'title' => 'Custom Software Development',
                'status' => 'draft',
                'display_order' => 1,
                'description' => 'Build tailored systems for operations.',
            ])
            ->assertRedirect(route('admin.content.index'));

        $this->actingAs($admin)
            ->post(route('admin.subscribers.store'), [
                'name' => 'Kofi',
                'email' => 'kofi@example.com',
                'source_page' => '/',
                'status' => 'subscribed',
            ])
            ->assertRedirect(route('admin.marketing.index'));

        $this->assertDatabaseHas('consultation_bookings', ['email' => 'akua@example.com']);
        $this->assertDatabaseHas('content_items', ['slug' => 'custom-software-development']);
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'kofi@example.com']);
    }
}
