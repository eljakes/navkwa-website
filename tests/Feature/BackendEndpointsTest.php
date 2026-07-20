<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackendEndpointsTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_chat_messages_are_saved_with_a_reply(): void
    {
        $response = $this->postJson(route('chat.messages.store'), [
            'message' => 'Can someone contact me about an ERP build?',
        ]);

        $response
            ->assertCreated()
            ->assertJsonCount(2, 'messages')
            ->assertJsonPath('messages.0.sender', 'user')
            ->assertJsonPath('messages.1.sender', 'support');

        $this->assertDatabaseHas('chat_messages', [
            'sender' => 'user',
            'message' => 'Can someone contact me about an ERP build?',
        ]);
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
            ->assertDontSee('Providers')
            ->assertDontSee('Website')
            ->assertDontSee('Paystack')
            ->assertDontSee('Hubtel');
    }

    public function test_payment_initialize_creates_demo_transaction_without_live_keys(): void
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

        $response->assertRedirect();

        $this->assertDatabaseHas('payment_transactions', [
            'provider' => 'paystack',
            'payment_method' => 'mobile_money',
            'mobile_network' => 'mtn_momo',
            'amount' => '125.50',
            'status' => 'demo',
        ]);
    }

    public function test_admin_portal_requires_authentication(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
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
            ->assertSee('Navkwa admin dashboard')
            ->assertSee('Dashboard Overview')
            ->assertSee('Contact Messages')
            ->assertSee('Website Content Management');
    }

    public function test_admin_can_update_and_export_enquiries(): void
    {
        $admin = User::factory()->create(['account_status' => 'active']);
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
            ->assertRedirect(route('admin.dashboard', ['section' => 'enquiries']));

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
        $admin = User::factory()->create(['account_status' => 'active']);
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
            ->assertRedirect(route('admin.dashboard', ['section' => 'leads']));

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
        $admin = User::factory()->create(['account_status' => 'active']);

        $this->actingAs($admin)
            ->post(route('admin.consultations.store'), [
                'client_name' => 'Akua Boateng',
                'email' => 'akua@example.com',
                'status' => 'Pending',
                'meeting_type' => 'Discovery call',
                'meeting_link' => 'https://meet.google.com/example',
            ])
            ->assertRedirect(route('admin.dashboard', ['section' => 'consultations']));

        $this->actingAs($admin)
            ->post(route('admin.content.store'), [
                'content_type' => 'Service',
                'title' => 'Custom Software Development',
                'status' => 'draft',
                'display_order' => 1,
                'description' => 'Build tailored systems for operations.',
            ])
            ->assertRedirect(route('admin.dashboard', ['section' => 'content']));

        $this->actingAs($admin)
            ->post(route('admin.subscribers.store'), [
                'name' => 'Kofi',
                'email' => 'kofi@example.com',
                'source_page' => '/',
                'status' => 'subscribed',
            ])
            ->assertRedirect(route('admin.dashboard', ['section' => 'marketing']));

        $this->assertDatabaseHas('consultation_bookings', ['email' => 'akua@example.com']);
        $this->assertDatabaseHas('content_items', ['slug' => 'custom-software-development']);
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'kofi@example.com']);
    }
}
