<?php

namespace Tests\Feature;

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
}
