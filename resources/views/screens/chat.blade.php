@php
    $initial = [
        'providers' => \App\Support\MockData::providers(),
        'system' =>
            'You are a helpful, articulate assistant. Provide clear, structured answers. When appropriate, use markdown formatting including headings, lists, and code blocks.',
        'provider' => null,
        'model' => null,
        'temperature' => 0.7,
        'maxTokens' => 4096,
        'topP' => 1.0,
        'messages' => [
            [
                'id' => 1,
                'role' => 'user',
                'content' => 'Can you explain the difference between REST and GraphQL APIs?',
                'timestamp' => now()->subMinutes(12)->toIso8601String(),
            ],
            [
                'id' => 2,
                'role' => 'assistant',
                'content' =>
                    "## REST vs GraphQL\n\nBoth are popular approaches for building APIs, but they differ in philosophy and flexibility.\n\n### REST (Representational State Transfer)\n\n- **Resource-based URLs** — each endpoint maps to a resource (`/users/123`)\n- **HTTP methods** define actions (`GET`, `POST`, `PUT`, `DELETE`)\n- **Fixed response shape** — the server determines what data is returned\n- **Multiple endpoints** for different data needs\n\n### GraphQL\n\n- **Single endpoint** — all queries go to one URL\n- **Client specifies fields** — request exactly the data you need\n- **Strongly typed schema** — the API contract is self-documenting\n- **No over/under-fetching** — get exactly what you ask for\n\n### When to use which?\n\n| Factor | REST | GraphQL |\n| --- | --- | --- |\n| Simplicity | Better for simple CRUD | Better for complex data |\n| Caching | HTTP caching built-in | Requires custom caching |\n| Learning curve | Lower | Higher |\n| File uploads | Native | Requires setup |\n| Real-time | Via WebSockets/SSE | Subscriptions built-in |\n\n**Bottom line:** REST is great for straightforward APIs with well-defined resources. GraphQL shines when you have complex, interconnected data and want to minimize round trips.\n\nWant me to dive deeper into any of these aspects?",
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
                'content' =>
                    "## Authentication in REST vs GraphQL\n\nBoth handle auth similarly at the transport level — the difference is mainly in implementation patterns.\n\n### Common approaches for both:\n\n1. **JWT (JSON Web Tokens)** — Stateless, self-contained tokens\n2. **OAuth 2.0** — Token-based with authorization server\n3. **API Keys** — Simple but less secure\n\n### REST Auth patterns\n\n```\nGET /api/users/me\nAuthorization: Bearer <jwt_token>\n```\n\n- Tokens passed in headers (standard)\n- Per-endpoint middleware for authorization\n- Easy to cache auth state\n\n### GraphQL Auth patterns\n\n```graphql\nquery {\n  me {\n    name\n    email\n  }\n}\n```\n\n- Same header-based auth\n- Resolver-level authorization (field-level)\n- More granular but more complex to implement\n\n### Recommendation\n\n**Use the same auth mechanism for both.** The transport layer (REST vs GraphQL) doesn't dictate your auth strategy. JWT + OAuth 2.0 works great with either.\n\nThe key advantage of GraphQL is **field-level authorization** — you can control access per field, not just per endpoint.\n\nShall I show a concrete implementation example?",
                'timestamp' => now()->subMinutes(4)->toIso8601String(),
                'tokensIn' => 32,
                'tokensOut' => 201,
                'cost' => 0.0009,
                'status' => 'completed',
                'model' => 'claude-3-7-sonnet',
                'provider' => 'anthropic',
            ],
        ],
    ];
@endphp
<x-layouts.app>
    <x-app.page-container class="flex h-[calc(100dvh-4rem)] flex-col">
        <div x-data="pfChat({{ Js::from($initial) }})" x-cloak class="flex min-h-0 flex-1 flex-col">

            {{-- Header --}}
            <header
                class="flex shrink-0 items-center justify-between gap-4 border-b border-zinc-200 px-5 py-3 dark:border-white/10">
                <div class="min-w-0">
                    <p class="mb-0.5 text-xs font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">
                        Chat</p>
                    <div class="flex items-center gap-2">
                        <h1 class="truncate text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">
                            Conversation</h1>
                        <span
                            class="hidden items-center gap-1.5 rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium tabular-nums text-zinc-500 sm:inline-flex dark:bg-white/5 dark:text-zinc-400"
                            x-text="messageCount + ' messages'"></span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="showConfig = !showConfig"
                        class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-200"
                        :class="showConfig ? 'bg-zinc-100 dark:bg-white/5' : ''">
                        <flux:icon.adjustments-horizontal class="size-3.5" />
                        <span class="hidden sm:inline" x-text="showConfig ? 'Hide config' : 'Show config'"></span>
                    </button>
                    <button type="button" x-on:click="clearChat()" :disabled="isStreaming || messages.length === 0"
                        class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-700 disabled:opacity-40 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-200">
                        <flux:icon.trash class="size-3.5" />
                        <span class="hidden sm:inline">Clear</span>
                    </button>
                </div>
            </header>

            {{-- Main area --}}
            <div class="flex min-h-0 flex-1 overflow-hidden">

                {{-- Left: message thread + input --}}
                <div class="flex min-h-0 min-w-0 flex-1 flex-col">

                    {{-- Messages --}}
                    <div x-ref="thread" class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                        {{-- Empty state --}}
                        <template x-if="messages.length === 0">
                            <div class="flex flex-col items-center justify-center gap-4 py-20 text-center">
                                <div
                                    class="grid size-14 place-items-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-white/5 dark:text-zinc-500">
                                    <flux:icon.chat-bubble-left-right class="size-6" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Start a conversation
                                    </p>
                                    <p class="mt-1 max-w-sm text-xs leading-relaxed text-zinc-400 dark:text-zinc-500">
                                        Ask questions, get explanations, write code, or brainstorm ideas. Your
                                        conversation history stays in this session.
                                    </p>
                                </div>
                            </div>
                        </template>

                        {{-- Message list --}}
                        <div class="mx-auto max-w-3xl space-y-6">
                            <template x-for="msg in messages" :key="msg.id">
                                <div x-data="{ show: false }" x-init="$nextTick(() => show = true)" x-show="show"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">

                                    {{-- User message --}}
                                    <template x-if="msg.role === 'user'">
                                        <div class="max-w-[85%] sm:max-w-[75%]">
                                            <div
                                                class="rounded-2xl rounded-br-md bg-brand-600 px-4 py-3 text-sm leading-relaxed text-white dark:bg-brand-500">
                                                <p class="whitespace-pre-wrap" x-text="msg.content"></p>
                                            </div>
                                            <p class="mt-1.5 text-right text-[0.65rem] text-zinc-400 dark:text-zinc-500"
                                                x-text="timeAgo(msg.timestamp)"></p>
                                        </div>
                                    </template>

                                    {{-- Assistant message --}}
                                    <template x-if="msg.role === 'assistant'">
                                        <div class="max-w-[85%] sm:max-w-[85%]">
                                            <div class="flex items-start gap-3">
                                                {{-- Avatar --}}
                                                <div
                                                    class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-[0.6rem] font-bold text-white dark:bg-zinc-700">
                                                    AI
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div
                                                        class="rounded-2xl rounded-tl-md border border-zinc-200 bg-white px-4 py-3 shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                                                        {{-- Streaming cursor --}}
                                                        <template x-if="msg.status === 'streaming'">
                                                            <div>
                                                                <div
                                                                    class="prose-chat prose prose-sm dark:prose-invert max-w-none">
                                                                    <div
                                                                        class="whitespace-pre-wrap text-sm leading-relaxed text-zinc-700 dark:text-zinc-200">
                                                                        <span x-text="msg.content"></span><span
                                                                            class="animate-caret inline-block h-[0.9em] w-[2px] translate-y-[0.1em] bg-brand-500"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        {{-- Completed content --}}
                                                        <template
                                                            x-if="msg.status === 'completed' || msg.status === 'cancelled'">
                                                            <div>
                                                                <div
                                                                    class="prose-chat prose prose-sm dark:prose-invert max-w-none">
                                                                    <div class="whitespace-pre-wrap text-sm leading-relaxed text-zinc-700 dark:text-zinc-200"
                                                                        x-html="renderMarkdown(msg.content)"></div>
                                                                </div>
                                                                <template x-if="msg.status === 'cancelled'">
                                                                    <div
                                                                        class="mt-2 rounded-md border border-dashed border-zinc-200 px-3 py-1.5 text-[0.65rem] text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                                                                        Response stopped mid-stream
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    {{-- Meta row --}}
                                                    <template x-if="msg.status === 'completed' && msg.tokensOut">
                                                        <div
                                                            class="mt-2 flex flex-wrap items-center gap-2 text-[0.65rem] tabular-nums text-zinc-400 dark:text-zinc-500">
                                                            <span
                                                                class="inline-flex items-center gap-1 rounded-md border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 dark:border-white/10 dark:bg-white/5">
                                                                <span class="font-mono text-zinc-500 dark:text-zinc-400"
                                                                    x-text="formatTokens(msg.tokensIn)"></span> in
                                                            </span>
                                                            <span
                                                                class="inline-flex items-center gap-1 rounded-md border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 dark:border-white/10 dark:bg-white/5">
                                                                <span class="font-mono text-zinc-500 dark:text-zinc-400"
                                                                    x-text="formatTokens(msg.tokensOut)"></span> out
                                                            </span>
                                                            <span
                                                                class="inline-flex items-center gap-1 rounded-md border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 dark:border-white/10 dark:bg-white/5">
                                                                $<span
                                                                    class="font-mono text-zinc-500 dark:text-zinc-400"
                                                                    x-text="(msg.cost || 0).toFixed(4)"></span>
                                                            </span>
                                                            <span
                                                                class="text-zinc-300 dark:text-zinc-600">&middot;</span>
                                                            <span x-text="timeAgo(msg.timestamp)"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Input area --}}
                    <div
                        class="shrink-0 border-t border-zinc-200 bg-zinc-50/80 px-4 py-3 backdrop-blur dark:border-white/10 dark:bg-zinc-950/80 sm:px-6">
                        <div class="mx-auto max-w-3xl">
                            <div
                                class="flex items-end gap-3 rounded-2xl border border-zinc-200 bg-white p-2 shadow-sm transition focus-within:border-brand-500/50 focus-within:ring-2 focus-within:ring-brand-500/20 dark:border-white/10 dark:bg-zinc-900/60 dark:focus-within:border-brand-400/50 dark:focus-within:ring-brand-400/20">
                                <textarea x-ref="chatInput" x-model="input" x-on:keydown="handleKeydown($event)" x-on:input="autoResize($event)"
                                    rows="1" placeholder="Type a message... (Shift+Enter for newline)" :disabled="isStreaming"
                                    class="max-h-[200px] min-h-[40px] flex-1 resize-none border-0 bg-transparent px-2 py-2 text-sm leading-relaxed text-zinc-900 placeholder:text-zinc-400 focus:outline-none disabled:opacity-50 dark:text-zinc-100 dark:placeholder:text-zinc-500"></textarea>

                                <div class="flex items-center gap-1.5">
                                    {{-- Stop button --}}
                                    <template x-if="isStreaming">
                                        <button type="button" x-on:click="stop()"
                                            class="inline-flex size-9 items-center justify-center rounded-xl bg-red-500 text-white shadow-sm transition hover:bg-red-600">
                                            <flux:icon.stop class="size-4" />
                                        </button>
                                    </template>

                                    {{-- Send button --}}
                                    <template x-if="!isStreaming">
                                        <button type="button" x-on:click="send()" :disabled="!input.trim()"
                                            class="inline-flex size-9 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm transition hover:bg-brand-700 disabled:opacity-30 disabled:hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600">
                                            <flux:icon.paper-airplane class="size-4" />
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div
                                class="mt-2 flex items-center justify-between text-[0.65rem] text-zinc-400 dark:text-zinc-500">
                                <span>
                                    <kbd
                                        class="rounded border border-zinc-200 bg-white px-1 py-0.5 font-mono text-[0.6rem] dark:border-white/10 dark:bg-white/5">Enter</kbd>
                                    to send
                                    <span class="mx-1">&middot;</span>
                                    <kbd
                                        class="rounded border border-zinc-200 bg-white px-1 py-0.5 font-mono text-[0.6rem] dark:border-white/10 dark:bg-white/5">Shift+Enter</kbd>
                                    for newline
                                </span>
                                <span x-show="isStreaming"
                                    class="flex items-center gap-1.5 text-brand-500 dark:text-brand-400">
                                    <span class="size-1.5 animate-pulse rounded-full bg-brand-500"></span>
                                    Streaming...
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: configuration panel --}}
                <div x-show="showConfig" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-2"
                    class="hidden w-80 shrink-0 overflow-y-auto border-l border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-900/60 lg:block">

                    <div class="space-y-5 p-4">
                        {{-- System prompt --}}
                        <div>
                            <button type="button" x-on:click="showSystemPrompt = !showSystemPrompt"
                                class="flex w-full items-center justify-between rounded-lg px-2 py-1.5 text-xs font-medium text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-200">
                                <span class="flex items-center gap-1.5">
                                    <flux:icon.document-text class="size-3.5" />
                                    System prompt
                                </span>
                                <flux:icon.chevron-down class="size-3.5 transition-transform"
                                    :class="showSystemPrompt ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="showSystemPrompt" x-transition class="mt-2">
                                <textarea x-model="system" rows="4"
                                    class="block w-full resize-y rounded-lg border-0 bg-zinc-50 px-3 py-2.5 font-mono text-xs leading-relaxed text-zinc-700 ring-1 ring-inset ring-zinc-200 transition placeholder:text-zinc-400 focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-300 dark:ring-white/10"
                                    placeholder="Define the assistant's behavior..."></textarea>
                            </div>
                        </div>

                        <div class="h-px bg-zinc-100 dark:bg-white/5"></div>

                        {{-- Provider --}}
                        <div>
                            <label class="mb-2 block text-xs font-medium text-zinc-400">Provider</label>
                            <div class="space-y-1.5">
                                <template x-for="p in providers" :key="p.slug">
                                    <button type="button" x-on:click="selectProvider(p.slug)"
                                        class="flex w-full items-center gap-2.5 rounded-lg border px-3 py-2 text-left text-sm transition"
                                        :class="provider === p.slug ?
                                            'border-brand-500/50 bg-brand-500/5 ring-1 ring-brand-500/20' :
                                            'border-zinc-200 hover:border-zinc-300 dark:border-white/10 dark:hover:border-white/15'">
                                        <span class="size-2 shrink-0 rounded-full"
                                            :style="'background-color:' + p.color"></span>
                                        <span
                                            class="min-w-0 flex-1 truncate font-medium text-zinc-700 dark:text-zinc-200"
                                            x-text="p.name"></span>
                                        <span class="text-[0.65rem] text-zinc-400"
                                            x-text="p.models.length + ' models'"></span>
                                        <flux:icon.check class="size-3.5 text-brand-600"
                                            x-show="provider === p.slug" />
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Model --}}
                        <div>
                            <label class="mb-2 block text-xs font-medium text-zinc-400">Model</label>
                            <select x-model="model" :disabled="isStreaming"
                                class="block w-full cursor-pointer rounded-lg border-0 bg-zinc-50 px-3 py-2.5 font-mono text-sm text-zinc-800 ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10">
                                <template x-for="m in models" :key="m.slug">
                                    <option x-bind:value="m.slug" x-text="m.name"></option>
                                </template>
                            </select>
                            <div
                                class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[0.65rem] text-zinc-400 dark:text-zinc-500">
                                <span class="inline-flex items-center gap-1">
                                    <flux:icon.window class="size-3" />
                                    <span class="font-mono text-zinc-500 dark:text-zinc-400"
                                        x-text="(currentModel.context || 0).toLocaleString()"></span> ctx
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <flux:icon.arrow-right-start-on-rectangle class="size-3" />
                                    <span class="font-mono text-zinc-500 dark:text-zinc-400"
                                        x-text="formatPrice((currentModel.pricing || {}).input || 0)"></span>
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <flux:icon.arrow-right-end-on-rectangle class="size-3" />
                                    <span class="font-mono text-zinc-500 dark:text-zinc-400"
                                        x-text="formatPrice((currentModel.pricing || {}).output || 0)"></span>
                                </span>
                            </div>
                        </div>

                        {{-- Parameters --}}
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-medium text-zinc-400">Temperature</label>
                                    <span class="font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300"
                                        x-text="temperature.toFixed(2)"></span>
                                </div>
                                <input type="range" min="0" max="2" step="0.05"
                                    x-model.number="temperature" :disabled="isStreaming"
                                    class="mt-2 w-full cursor-pointer accent-brand-600 disabled:opacity-60" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-medium text-zinc-400">Top P</label>
                                    <span class="font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300"
                                        x-text="topP.toFixed(2)"></span>
                                </div>
                                <input type="range" min="0" max="1" step="0.05"
                                    x-model.number="topP" :disabled="isStreaming"
                                    class="mt-2 w-full cursor-pointer accent-brand-600 disabled:opacity-60" />
                            </div>

                            <div>
                                <label class="text-xs font-medium text-zinc-400">Max tokens</label>
                                <input type="number" min="0" step="256" x-model.number="maxTokens"
                                    :disabled="isStreaming"
                                    class="mt-2 block w-full rounded-lg border-0 bg-zinc-50 px-3 py-2 font-mono text-sm text-zinc-800 ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 disabled:opacity-60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10" />
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <label class="text-xs font-medium text-zinc-400">Stream output</label>
                                    <p class="text-[0.65rem] text-zinc-400 dark:text-zinc-500">Receive tokens as
                                        generated.</p>
                                </div>
                                <button type="button" role="switch" x-on:click="stream = !stream"
                                    :disabled="isStreaming" :aria-checked="stream"
                                    class="relative inline-flex h-5 w-9 shrink-0 items-center rounded-full transition disabled:opacity-60"
                                    :class="stream ? 'bg-brand-600' : 'bg-zinc-300 dark:bg-white/15'">
                                    <span class="inline-block size-3.5 rounded-full bg-white shadow transition"
                                        :class="stream ? 'translate-x-[18px]' : 'translate-x-[3px]'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="h-px bg-zinc-100 dark:bg-white/5"></div>

                        {{-- Session stats --}}
                        <div>
                            <label class="mb-2 block text-xs font-medium text-zinc-400">Session stats</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div
                                    class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 dark:border-white/10 dark:bg-white/5">
                                    <p class="text-[0.65rem] text-zinc-400 dark:text-zinc-500">Total in</p>
                                    <p class="font-mono text-sm font-semibold tabular-nums text-zinc-700 dark:text-zinc-200"
                                        x-text="formatTokens(totalTokensIn)"></p>
                                </div>
                                <div
                                    class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 dark:border-white/10 dark:bg-white/5">
                                    <p class="text-[0.65rem] text-zinc-400 dark:text-zinc-500">Total out</p>
                                    <p class="font-mono text-sm font-semibold tabular-nums text-zinc-700 dark:text-zinc-200"
                                        x-text="formatTokens(totalTokensOut)"></p>
                                </div>
                                <div
                                    class="col-span-2 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 dark:border-white/10 dark:bg-white/5">
                                    <p class="text-[0.65rem] text-zinc-400 dark:text-zinc-500">Estimated cost</p>
                                    <p
                                        class="font-mono text-sm font-semibold tabular-nums text-brand-600 dark:text-brand-400">
                                        $<span x-text="totalCost.toFixed(4)"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app.page-container>

    {{-- Minimal markdown renderer (lightweight, no library needed) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pfChat', (initial) => {
                return {
                    ...initial,

                    input: '',
                    showConfig: false,
                    showSystemPrompt: false,
                    isStreaming: false,

                    get messageCount() {
                        return this.messages.length;
                    },

                    renderMarkdown(text) {
                        return window.renderMarkdown(text);
                    },
                };
            });
        });

        window.renderMarkdown = function(text) {
            if (!text) return '';

            let html = String(text)
                // Escape HTML first
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')

                // Code blocks
                .replace(
                    /```(\w*)\n([\s\S]*?)```/g,
                    '<pre class="rounded-lg bg-zinc-100 px-3.5 py-3 font-mono text-xs leading-relaxed text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><code>$2</code></pre>'
                )

                // Inline code
                .replace(
                    /`([^`]+)`/g,
                    '<code class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-brand-700 dark:bg-zinc-800 dark:text-brand-300">$1</code>'
                )

                // Bold
                .replace(
                    /\*\*(.+?)\*\*/g,
                    '<strong>$1</strong>'
                )

                // Italic
                .replace(
                    /\*(.+?)\*/g,
                    '<em>$1</em>'
                )

                // Headings
                .replace(
                    /^### (.+)$/gm,
                    '<h3 class="mt-4 mb-1 text-sm font-semibold text-zinc-900 dark:text-white">$1</h3>'
                )
                .replace(
                    /^## (.+)$/gm,
                    '<h2 class="mt-5 mb-2 text-base font-semibold text-zinc-900 dark:text-white">$1</h2>'
                )
                .replace(
                    /^# (.+)$/gm,
                    '<h1 class="mt-6 mb-2 text-lg font-bold text-zinc-900 dark:text-white">$1</h1>'
                )

                // Horizontal rule
                .replace(
                    /^---$/gm,
                    '<hr class="my-4 border-zinc-200 dark:border-white/10">'
                )

                // Unordered lists
                .replace(
                    /^- (.+)$/gm,
                    '<li class="ml-4 list-disc">$1</li>'
                )

                // Ordered lists
                .replace(
                    /^\d+\. (.+)$/gm,
                    '<li class="ml-4 list-decimal">$1</li>'
                )

                // Paragraph breaks
                .replace(/\n\n/g, '</p><p class="mt-2">')

                // Single line breaks
                .replace(/\n/g, '<br>');

            return '<p class="mt-2">' + html + '</p>';
        };
    </script>
</x-layouts.app>
