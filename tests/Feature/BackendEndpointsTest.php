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
}
