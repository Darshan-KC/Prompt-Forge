<?php

namespace App\Livewire\Chat;

use App\Ai\Agents\ChatAgent;
use App\Support\MockData;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Server-backed chat conversation.
 *
 * Owns the conversation state (messages, provider/model config, generation
 * parameters) so nothing lives in the browser DOM. The client only animates
 * the streaming preview and reports the finalised turn back via
 * {@see self::finishStream()} / {@see self::cancelStream()}.
 *
 * Responses are generated locally for now (mock). Swap {@see self::buildResponse()}
 * for a real [[\App\Ai\Agents\ChatAgent]] call to go live.
 */
#[Layout('components.layouts.app', ['title' => 'Chat'])]
class Chat extends Component
{
    public array $messages = [];

    public string $system = 'You are a helpful, articulate assistant. Provide clear, structured answers. When appropriate, use markdown formatting including headings, lists, and code blocks.';

    public string $provider = 'anthropic';

    public string $model = 'claude-3-7-sonnet';

    public float $temperature = 0.7;

    public int $maxTokens = 4096;

    public float $topP = 1.0;

    public bool $isStreaming = false;

    public function mount(): void
    {
        if ($this->messages === []) {
            $this->messages = $this->seedMessages();
        }
    }

    /**
     * Queue the user turn and stamp out the assistant response placeholder.
     *
     * The returned payload is what the client animates into the placeholder;
     * the finished result is persisted via {@see self::finishStream()}.
     *
     * @return array{id: string, content: string, tokensIn: int}|null
     */
    public function send(string $text = ''): ?array
    {
        $text = trim($text);

        if ($text === '' || $this->isStreaming) {
            return null;
        }

        $this->isStreaming = true;

        dd((new ChatAgent())->prompt($text));

        $this->messages[] = [
            'id' => 'u-'.uniqid(),
            'role' => 'user',
            'content' => $text,
            'timestamp' => now()->toIso8601String(),
        ];

        $tokensIn = $this->estimateTokens($this->system.' '.$text);

        $id = 'a-'.uniqid();
        $this->messages[] = [
            'id' => $id,
            'role' => 'assistant',
            'content' => '',
            'timestamp' => now()->toIso8601String(),
            'tokensIn' => $tokensIn,
            'tokensOut' => 0,
            'cost' => 0,
            'status' => 'streaming',
            'model' => $this->model,
            'provider' => $this->provider,
        ];

        return [
            'id' => $id,
            'content' => $this->buildResponse($text),
            'tokensIn' => $tokensIn,
        ];
    }

    /**
     * Persist a fully streamed assistant turn.
     */
    public function finishStream(string $id, string $content = '', int $tokensOut = 0): void
    {
        $this->resolveTurn($id, $content, $tokensOut, status: 'completed');
    }

    /**
     * Persist an aborted assistant turn.
     */
    public function cancelStream(string $id, string $content = ''): void
    {
        $this->resolveTurn($id, $content, 0, status: 'cancelled');
    }

    protected function resolveTurn(string $id, string $content, int $tokensOut, string $status): void
    {
        foreach ($this->messages as $index => $message) {
            if ($message['id'] !== $id) {
                continue;
            }

            $pricing = (MockData::model($this->model, $this->provider)['pricing'] ?? ['input' => 0, 'output' => 0]);

            $this->messages[$index]['content'] = $content;
            $this->messages[$index]['tokensOut'] = $tokensOut;
            $this->messages[$index]['cost'] = (
                ($message['tokensIn'] * ($pricing['input'] ?? 0) + $tokensOut * ($pricing['output'] ?? 0)) / 1e6
            );
            $this->messages[$index]['status'] = $status;

            break;
        }

        $this->isStreaming = false;
    }

    public function clearChat(): void
    {
        if ($this->isStreaming) {
            return;
        }

        $this->messages = [];
    }

    public function selectProvider(string $slug): void
    {
        if ($this->isStreaming || MockData::provider($slug) === null) {
            return;
        }

        $this->provider = $slug;
        $this->model = MockData::provider($slug)['models'][0]['slug'] ?? $this->model;
    }

    public function formatPrice(float $perMillion): string
    {
        return number_format($perMillion, 2).' / 1M';
    }

    public function formatTokens(int $count): string
    {
        return number_format($count);
    }

    protected function estimateTokens(string $text): int
    {
        return max(1, (int) ceil(mb_strlen($text) / 4));
    }

    /**
     * Canned, keyword-matched mock responses.
     *
     * TODO: Replace with a real [[\App\Ai\Agents\ChatAgent]] prompt so the chat
     * returns live model output.
     */
    protected function buildResponse(string $userText): string
    {
        $lower = mb_strtolower($userText);

        if (str_contains($lower, 'hello') || str_contains($lower, 'hi') || str_contains($lower, 'hey')) {
            return <<<'MD'
                Hello! I'm your AI assistant. I can help with

                - **Code review** and debugging
                - **Writing** and editing
                - **Research** and analysis
                - **Math** and logic problems

                What would you like to work on today?
                MD;
        }

        if (str_contains($lower, 'code') || str_contains($lower, 'function') || str_contains($lower, 'implement')) {
            return <<<'MD'
                # Implementation Plan

                Here's a structured approach to the coding task:

                ## 1. Break it down
                Split the problem into smaller, testable units.

                ## 2. Define the interface
                ```typescript
                interface Result {
                  success: boolean;
                  data: unknown;
                  error?: string;
                }
                ```

                ## 3. Implement core logic
                Write the main function with proper error handling,
                then add edge case coverage.

                ## 4. Test
                Create unit tests for each branch, then integration
                tests for the full flow.

                Want me to write the full implementation for a
                specific language or framework?
                MD;
        }

        if (str_contains($lower, 'explain') || str_contains($lower, 'what is') || str_contains($lower, 'how does')) {
            return <<<'MD'
                ## Explanation

                Let me break this down clearly:

                **Core concept:** The idea works by establishing a
                chain of transformations that convert raw input into
                structured output.

                **Key components:**
                1. **Input layer** — receives and validates the data
                2. **Processing** — applies the transformation rules
                3. **Output** — formats the result for consumption

                **Why it matters:**
                This pattern is widely used because it provides:
                - Clear separation of concerns
                - Easy testing at each stage
                - Flexibility to swap components

                Would you like a deeper dive into any of these
                components?
                MD;
        }

        return <<<'MD'
            ## Response

            Great question. Here's my analysis:

            ### Key Points

            1. **Context matters** — The answer depends on your
               specific use case and constraints.

            2. **Trade-offs** — Every approach has pros and cons.
               The best choice balances complexity against
               maintainability.

            3. **Iteration** — Start with the simplest version that
               works, then refine based on real feedback.

            ### Recommendation

            I'd suggest starting with a minimal prototype to
            validate the approach, then expanding based on what
            you learn.

            ```markdown
            Tip: You can save this conversation as a prompt
            template from the sidebar menu.
            ```

            Want me to elaborate on any of these points?
            MD;
    }

    /**
     * Demo messages shown on first load so the screen doesn't feel empty.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function seedMessages(): array
    {
        return [
            [
                'id' => 1,
                'role' => 'user',
                'content' => 'Can you explain the difference between REST and GraphQL APIs?',
                'timestamp' => now()->subMinutes(12)->toIso8601String(),
            ],
            [
                'id' => 2,
                'role' => 'assistant',
                'content' => <<<'MD'
                    ## REST vs GraphQL

                    Both are popular approaches for building APIs, but they differ in philosophy and flexibility.

                    ### REST (Representational State Transfer)

                    - **Resource-based URLs** — each endpoint maps to a resource (`/users/123`)
                    - **HTTP methods** define actions (`GET`, `POST`, `PUT`, `DELETE`)
                    - **Fixed response shape** — the server determines what data is returned
                    - **Multiple endpoints** for different data needs

                    ### GraphQL

                    - **Single endpoint** — all queries go to one URL
                    - **Client specifies fields** — request exactly the data you need
                    - **Strongly typed schema** — the API contract is self-documenting
                    - **No over/under-fetching** — get exactly what you ask for

                    ### When to use which?

                    | Factor | REST | GraphQL |
                    | --- | --- | --- |
                    | Simplicity | Better for simple CRUD | Better for complex data |
                    | Caching | HTTP caching built-in | Requires custom caching |
                    | Learning curve | Lower | Higher |
                    | File uploads | Native | Requires setup |
                    | Real-time | Via WebSockets/SSE | Subscriptions built-in |

                    **Bottom line:** REST is great for straightforward APIs with well-defined resources. GraphQL shines when you have complex, interconnected data and want to minimize round trips.

                    Want me to dive deeper into any of these aspects?
                    MD,
                'timestamp' => now()->subMinutes(11)->toIso8601String(),
                'tokensIn' => 24,
                'tokensOut' => 187,
                'cost' => 0.0008,
                'status' => 'completed',
                'model' => 'claude-3-7-sonnet',
                'provider' => 'anthropic',
            ],
            [
                'id' => 3,
                'role' => 'user',
                'content' => 'What about authentication? Which is better for handling auth tokens?',
                'timestamp' => now()->subMinutes(5)->toIso8601String(),
            ],
            [
                'id' => 4,
                'role' => 'assistant',
                'content' => <<<'MD'
                    ## Authentication in REST vs GraphQL

                    Both handle auth similarly at the transport level — the difference is mainly in implementation patterns.

                    ### Common approaches for both:

                    1. **JWT (JSON Web Tokens)** — Stateless, self-contained tokens
                    2. **OAuth 2.0** — Token-based with authorization server
                    3. **API Keys** — Simple but less secure

                    ### REST Auth patterns

                    ```
                    GET /api/users/me
                    Authorization: Bearer <jwt_token>
                    ```

                    - Tokens passed in headers (standard)
                    - Per-endpoint middleware for authorization
                    - Easy to cache auth state

                    ### GraphQL Auth patterns

                    ```graphql
                    query {
                      me {
                        name
                        email
                      }
                    }
                    ```

                    - Same header-based auth
                    - Resolver-level authorization (field-level)
                    - More granular but more complex to implement

                    ### Recommendation

                    **Use the same auth mechanism for both.** The transport layer (REST vs GraphQL) doesn't dictate your auth strategy. JWT + OAuth 2.0 works great with either.

                    The key advantage of GraphQL is **field-level authorization** — you can control access per field, not just per endpoint.

                    Shall I show a concrete implementation example?
                    MD,
                'timestamp' => now()->subMinutes(4)->toIso8601String(),
                'tokensIn' => 32,
                'tokensOut' => 201,
                'cost' => 0.0009,
                'status' => 'completed',
                'model' => 'claude-3-7-sonnet',
                'provider' => 'anthropic',
            ],
        ];
    }

    public function render()
    {
        $providers = MockData::providers();

        $models = collect($this->currentProvider($providers)['models'] ?? [])
            ->map(fn (array $model) => $model + ['provider' => $this->provider])
            ->values()
            ->all();

        $currentModel = collect($models)->firstWhere('slug', $this->model) ?? [];

        $tokensIn = array_sum(array_column($this->messages, 'tokensIn'));
        $tokensOut = array_sum(array_column($this->messages, 'tokensOut'));
        $cost = array_sum(array_column($this->messages, 'cost'));

        return view('livewire.chat.chat', [
            'providers' => $providers,
            'models' => $models,
            'currentModel' => $currentModel,
            'totals' => ['tokensIn' => $tokensIn, 'tokensOut' => $tokensOut, 'cost' => $cost],
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $providers
     * @return array<string, mixed>
     */
    protected function currentProvider(array $providers): array
    {
        foreach ($providers as $provider) {
            if ($provider['slug'] === $this->provider) {
                return $provider;
            }
        }

        return [];
    }
}
