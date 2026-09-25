<?php

namespace Tests\Feature;

use App\Livewire\Chat\Chat;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_chat_route_renders_the_component(): void
    {
        $user = new User(['name' => 'Darshan', 'email' => 'darshan@promptforge.test']);

        $this->actingAs($user)
            ->get('/chat')
            ->assertOk()
            ->assertSee('Conversation');
    }

    public function test_component_is_seeded_with_demo_conversation(): void
    {
        Livewire::test(Chat::class)
            ->assertSet('provider', 'anthropic')
            ->assertSet('model', 'claude-3-7-sonnet')
            ->assertCount('messages', 4);
    }

    public function test_send_stamps_out_user_and_assistant_turns(): void
    {
        $component = Livewire::test(Chat::class)
            ->call('send', '  Can you explain caching?  ')
            ->assertSet('isStreaming', true)
            ->assertCount('messages', 6);

        $messages = $component->get('messages');

        $this->assertSame('user', $messages[4]['role']);
        $this->assertSame('Can you explain caching?', $messages[4]['content']);
        $this->assertSame('assistant', $messages[5]['role']);
        $this->assertSame('streaming', $messages[5]['status']);
        $this->assertSame(0, $messages[5]['tokensOut']);
    }

    public function test_send_returns_the_payload_the_client_animates(): void
    {
        Livewire::test(Chat::class)
            ->call('send', 'hello there')
            ->assertReturned(fn (mixed $payload) => is_array($payload)
                && trim($payload['content']) !== ''
                && $payload['tokensIn'] > 0);
    }

    public function test_send_ignores_empty_text(): void
    {
        Livewire::test(Chat::class)
            ->call('send', "   \n  ")
            ->assertSet('isStreaming', false)
            ->assertCount('messages', 4);
    }

    public function test_finish_stream_persists_the_completed_turn(): void
    {
        $component = Livewire::test(Chat::class)
            ->call('send', 'hello there');

        $messages = $component->get('messages');
        $id = $messages[5]['id'];
        $tokensIn = $messages[5]['tokensIn'];

        $component
            ->call('finishStream', $id, 'Hello! How can I help?', 12)
            ->assertSet('isStreaming', false)
            ->assertCount('messages', 6);

        $finished = $component->get('messages')[5];

        $this->assertSame('completed', $finished['status']);
        $this->assertSame('Hello! How can I help?', $finished['content']);
        $this->assertSame(12, $finished['tokensOut']);
        $this->assertGreaterThan(0, $finished['cost']);
        $this->assertSame($tokensIn, $finished['tokensIn']);
    }

    public function test_cancel_stream_marks_the_turn_cancelled(): void
    {
        $component = Livewire::test(Chat::class)
            ->call('send', 'hello there');

        $id = $component->get('messages')[5]['id'];

        $component
            ->call('cancelStream', $id, 'Hello')
            ->assertSet('isStreaming', false);

        $cancelled = $component->get('messages')[5];
        $this->assertSame('cancelled', $cancelled['status']);
        $this->assertSame('Hello', $cancelled['content']);
    }

    public function test_clear_chat_empties_the_conversation(): void
    {
        Livewire::test(Chat::class)
            ->call('clearChat')
            ->assertSet('messages', []);
    }

    public function test_select_provider_switches_to_its_first_model(): void
    {
        Livewire::test(Chat::class)
            ->call('selectProvider', 'openai')
            ->assertSet('provider', 'openai')
            ->assertSet('model', 'gpt-4o');
    }
}
