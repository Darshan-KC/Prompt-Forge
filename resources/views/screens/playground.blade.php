<x-layouts.app>
@php
    $pendingPromptId = isset($prompt) && is_numeric($prompt) ? (int) $prompt : null;
    $loaded = $pendingPromptId !== null ? \App\Support\MockData::prompt($pendingPromptId) : null;

    $initial = [
        'providers' => \App\Support\MockData::providers(),
        'system' => $loaded['system'] ?? 'You are a meticulous, senior-level assistant providing structured, high-quality answers. Follow the user\'s instructions exactly and match the requested output format.',
        'promptText' => $loaded['prompt'] ?? '',
        'variables' => $loaded['variables'] ?? [],
        'provider' => $loaded['provider'] ?? null,
        'model' => $loaded['model'] ?? null,
        'temperature' => $loaded['temperature'] ?? null,
        'maxTokens' => $loaded['maxTokens'] ?? null,
        'topP' => $loaded['topP'] ?? null,
    ];
@endphp

<x-app.page-container class="space-y-6">
    <div x-data="pfPlayground({{ Js::from($initial) }})" x-cloak>
        <x-shared.page-header
            eyebrow="Playground"
            title="Test and compare every prompt"
            description="Iterate on system instructions and messages, then execute against any connected model."
            kbd="⌘↵ run"
        >
            <x-slot:actions>
                <flux:button href="{{ route('prompts.create') }}" wire:navigate variant="subtle" icon="book-open" class="hidden sm:inline-flex">
                    Prompt library
                </flux:button>
                <flux:button variant="primary" icon="play" x-on:click="run()" x-show="!isBusy()"
                    x-text="output ? 'Run again' : 'Run prompt'">
                </flux:button>
                <flux:button variant="danger" icon="stop" x-on:click="stop()" x-show="isBusy()" x-cloak>
                    Stop
                </flux:button>
            </x-slot:actions>
        </x-shared.page-header>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_400px]">
            {{-- LEFT: editor + output --}}
            <div class="min-w-0 space-y-6">
                {{-- Editor --}}
                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="flex items-center justify-between gap-2 border-b border-zinc-100 px-4 py-2.5 sm:px-5 dark:border-white/5">
                        <div class="flex items-center gap-1 rounded-lg bg-zinc-100 p-1 sm:w-auto dark:bg-white/5">
                            <button type="button" class="rounded-md px-3 py-1 text-xs font-medium transition"
                                :class="tab === 'system' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'"
                                x-on:click="tab = 'system'">System</button>
                            <button type="button" class="rounded-md px-3 py-1 text-xs font-medium transition"
                                :class="tab === 'message' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'"
                                x-on:click="tab = 'message'">Message</button>
                            <button type="button" class="rounded-md px-3 py-1 text-xs font-medium transition"
                                :class="tab === 'interpolated' ? 'bg-white text-zinc-900 shadow-xs dark:bg-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-zinc-400'"
                                x-on:click="tab = 'interpolated'">Interpolated</button>
                        </div>
                        <span class="hidden text-xs text-zinc-400 sm:block dark:text-zinc-500" x-text="estimatedInputTokens.toLocaleString() + ' ~input tokens'"></span>
                    </header>

                    <div class="p-4 sm:p-5">
                        <template x-if="tab === 'system'">
                            <div>
                                <label class="text-xs font-medium text-zinc-400" for="pf-system">System instructions</label>
                                <textarea id="pf-system" x-model="system"
                                    class="mt-2 block min-h-[180px] w-full resize-y rounded-lg border-0 bg-zinc-50 px-3.5 py-3 font-mono text-sm leading-relaxed text-zinc-800 ring-1 ring-inset ring-zinc-200 transition placeholder:text-zinc-400 focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10 dark:focus:ring-brand-400/60"
                                    placeholder="Define the role, constraints, output format and tone of the model..."></textarea>
                            </div>
                        </template>

                        <template x-if="tab === 'message'">
                            <div>
                                <label class="text-xs font-medium text-zinc-400" for="pf-prompt">User message</label>
                                <textarea id="pf-prompt" x-ref="promptField" x-model="promptText"
                                    class="mt-2 block min-h-[240px] w-full resize-y rounded-lg border-0 bg-zinc-50 px-3.5 py-3 font-mono text-sm leading-relaxed text-zinc-800 ring-1 ring-inset ring-zinc-200 transition placeholder:text-zinc-400 focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10 dark:focus:ring-brand-400/60"
                                    placeholder="Write your prompt here. Wrap dynamic values in &#123;&#123; curly braces &#125;&#125; — they become variables..."></textarea>
                            </div>
                        </template>

                        <template x-if="tab === 'interpolated'">
                            <div>
                                <label class="text-xs font-medium text-zinc-400" for="pf-interpolated">Interpolated message</label>
                                <pre id="pf-interpolated" x-text="interpolated || 'Nothing to preview — write a message first.'"
                                    class="mt-2 block min-h-[240px] w-full whitespace-pre-wrap rounded-lg border border-zinc-200 bg-zinc-50 px-3.5 py-3 font-mono text-sm leading-relaxed text-zinc-700 dark:border-white/10 dark:bg-zinc-950/40 dark:text-zinc-300"></pre>
                            </div>
                        </template>

                        {{-- Variable chips --}}
                        <div class="mt-3 flex flex-wrap items-center gap-1.5" x-show="variables.length > 0" x-transition>
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">Insert:</span>
                            <template x-for="v in variables" :key="v.key">
                                <button type="button" x-on:click="insertVariable(v.key)"
                                    class="inline-flex items-center gap-1 rounded-md border border-brand-500/20 bg-brand-500/5 px-2 py-0.5 font-mono text-xs text-brand-700 transition hover:bg-brand-500/10 dark:text-brand-300"
                                    x-text="tokenFor(v.key)"></button>
                            </template>
                        </div>
                    </div>
                </section>

                {{-- Output --}}
                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="flex items-center justify-between gap-2 border-b border-zinc-100 px-4 py-3 sm:px-5 dark:border-white/5">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Output</h2>
                            <span class="hidden items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium sm:inline-flex"
                                :class="{
                                    'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300': status === 'idle',
                                    'bg-brand-500/10 text-brand-700 dark:text-brand-300': status === 'running' || status === 'streaming',
                                    'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300': status === 'completed',
                                    'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300': status === 'cancelled'
                                }">
                                <span class="size-1.5 rounded-full bg-current"
                                    :class="{ 'animate-pulse': status === 'streaming' || status === 'running' }"
                                    x-show="status !== 'idle'"></span>
                                <span x-text="{ idle: 'Idle', running: 'Queued', streaming: 'Streaming', completed: 'Completed', cancelled: 'Stopped' }[status]"></span>
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-xs tabular-nums text-zinc-400 dark:text-zinc-500">
                            <span x-show="status === 'streaming' || status === 'completed'" x-text="formattedElapsed"></span>
                            <span x-show="status !== 'idle'" x-text="isBusy() ? '…' : formattedTokensOut + ' tokens'"></span>
                        </div>
                    </header>

                    <div class="min-h-[320px]">
                        {{-- Idle --}}
                        <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center" x-show="status === 'idle'" x-cloak>
                            <div class="grid size-12 place-items-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-white/5 dark:text-zinc-500">
                                <flux:icon.play class="size-5" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Ready when you are</p>
                                <p class="mt-1 max-w-sm text-xs leading-relaxed text-zinc-400 dark:text-zinc-500">
                                    Configure a model on the right, then run this prompt. Streaming output, token counts and cost appear here.
                                </p>
                            </div>
                            <flux:button variant="primary" size="sm" icon="play" x-on:click="run()">Run prompt</flux:button>
                        </div>

                        {{-- Queued --}}
                        <div class="space-y-2.5 p-5 sm:p-6" x-show="status === 'running'" x-cloak>
                            <div class="h-3 w-4/5 animate-pulse rounded bg-zinc-100 dark:bg-white/10"></div>
                            <div class="h-3 w-3/5 animate-pulse rounded bg-zinc-100 dark:bg-white/10"></div>
                            <div class="h-3 w-2/3 animate-pulse rounded bg-zinc-100 dark:bg-white/10"></div>
                            <div class="pt-1 text-xs text-zinc-400 dark:text-zinc-500">Warming up the provider connection…</div>
                        </div>

                        {{-- Streaming / completed / cancelled --}}
                        <div x-show="status !== 'idle' && status !== 'running'" x-cloak class="px-4 py-3 sm:px-5">
                            <pre class="whitespace-pre-wrap font-mono text-sm leading-relaxed text-zinc-700 dark:text-zinc-200"><span x-text="output"></span><span x-show="status === 'streaming'" class="animate-caret inline-block h-[0.9em] w-[2px] translate-y-[0.1em] bg-brand-500"></span></pre>

                            <template x-if="status === 'cancelled'">
                                <div class="mt-4 rounded-lg border border-dashed border-zinc-300 px-3.5 py-3 text-xs text-zinc-500 dark:border-white/15 dark:text-zinc-400">
                                    Run stopped mid-stream. The partial output above was not saved.
                                    <button type="button" class="ml-1 font-medium text-brand-600 dark:text-brand-400" x-on:click="reset()">Clear output</button>
                                </div>
                            </template>

                            {{-- Completed meta --}}
                            <div x-show="status === 'completed'" x-cloak class="mt-5 flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-4 dark:border-white/5">
                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                                    In <b class="font-semibold" x-text="tokensIn.toLocaleString()"></b>
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                                    Out <b class="font-semibold" x-text="formattedTokensOut"></b>
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                                    Latency <b class="font-semibold" x-text="formattedElapsed"></b>
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                                    Cost <b class="font-semibold" x-text="'$' + cost.toFixed(4)"></b>
                                </span>
                                <div class="ml-auto flex items-center gap-2">
                                    <button type="button" x-on:click="reset()" class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-200">
                                        <flux:icon.arrow-path class="size-3.5" /> Clear
                                    </button>
                                    <button type="button"
                                        x-data="{ copied: false }"
                                        x-on:click="navigator.clipboard.writeText(output); copied = true; setTimeout(() => copied = false, 1500)"
                                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-200">
                                        <flux:icon.clipboard-document x-show="!copied" class="size-3.5" />
                                        <flux:icon.check x-show="copied" class="size-3.5 text-emerald-500" />
                                        <span x-text="copied ? 'Copied' : 'Copy'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- RIGHT: configuration + variables --}}
            <div class="min-w-0 space-y-6">
                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="flex items-center justify-between border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Configuration</h2>
                        <flux:icon.adjustments-horizontal class="size-4 text-zinc-400 dark:text-zinc-500" />
                    </header>
                    <div class="space-y-5 p-4 sm:p-5">
                        {{-- Provider --}}
                        <div>
                            <label class="text-xs font-medium text-zinc-400">Provider</label>
                            <div class="mt-2 grid grid-cols-1 gap-1.5">
                                <template x-for="p in providers" :key="p.slug">
                                    <button type="button"
                                        x-on:click="selectProvider(p.slug)"
                                        class="flex items-center gap-2.5 rounded-lg border px-3 py-2 text-left text-sm transition"
                                        :class="provider === p.slug ? 'border-brand-500/50 bg-brand-500/5 ring-1 ring-brand-500/20' : 'border-zinc-200 hover:border-zinc-300 dark:border-white/10 dark:hover:border-white/15'">
                                        <span class="size-2 shrink-0 rounded-full" :style="'background-color:' + p.color"></span>
                                        <span class="min-w-0 flex-1 truncate font-medium text-zinc-700 dark:text-zinc-200" x-text="p.name"></span>
                                        <span class="text-[0.65rem] text-zinc-400" x-text="p.models.length + ' models'"></span>
                                        <flux:icon.check class="size-3.5 text-brand-600" x-show="provider === p.slug" />
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Model --}}
                        <div>
                            <label class="text-xs font-medium text-zinc-400">Model</label>
                            <div class="mt-2">
                                <select x-model="model" :disabled="isBusy()"
                                    class="block w-full cursor-pointer rounded-lg border-0 bg-zinc-50 px-3 py-2.5 font-mono text-sm text-zinc-800 ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10">
                                    <template x-for="m in models" :key="m.slug">
                                        <option x-bind:value="m.slug" x-text="m.name + ' — ' + m.slug"></option>
                                    </template>
                                </select>
                                <div class="mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-[0.7rem] text-zinc-400 dark:text-zinc-500">
                                    <span class="inline-flex items-center gap-1">
                                        <flux:icon.window class="size-3" />
                                        Context <b class="ml-0.5 font-mono font-medium text-zinc-600 dark:text-zinc-300" x-text="(currentModel.context || 0).toLocaleString()"></b>
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <flux:icon.arrow-right-start-on-rectangle class="size-3" />
                                        <b class="ml-0.5 font-mono font-medium text-zinc-600 dark:text-zinc-300" x-text="formatPrice((currentModel.pricing || {}).input || 0)"></b>
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <flux:icon.arrow-right-end-on-rectangle class="size-3" />
                                        <b class="ml-0.5 font-mono font-medium text-zinc-600 dark:text-zinc-300" x-text="formatPrice((currentModel.pricing || {}).output || 0)"></b>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Parameters --}}
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-medium text-zinc-400" for="pf-temp">Temperature</label>
                                    <span class="font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300" x-text="temperature.toFixed(2)"></span>
                                </div>
                                <input id="pf-temp" type="range" min="0" max="2" step="0.05" x-model.number="temperature" :disabled="isBusy()"
                                    class="mt-2 w-full cursor-pointer accent-brand-600 disabled:opacity-60" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-medium text-zinc-400" for="pf-topp">Top P</label>
                                    <span class="font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300" x-text="topP.toFixed(2)"></span>
                                </div>
                                <input id="pf-topp" type="range" min="0" max="1" step="0.05" x-model.number="topP" :disabled="isBusy()"
                                    class="mt-2 w-full cursor-pointer accent-brand-600 disabled:opacity-60" />
                            </div>

                            <div>
                                <label class="text-xs font-medium text-zinc-400" for="pf-maxtokens">Max tokens</label>
                                <input id="pf-maxtokens" type="number" min="0" step="256" x-model.number="maxTokens" :disabled="isBusy()"
                                    class="mt-2 block w-full rounded-lg border-0 bg-zinc-50 px-3 py-2 font-mono text-sm text-zinc-800 ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 disabled:opacity-60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10" />
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <label class="text-xs font-medium text-zinc-400">Stream output</label>
                                    <p class="text-[0.7rem] text-zinc-400 dark:text-zinc-500">Receive tokens as they are generated.</p>
                                </div>
                                <button type="button" role="switch" x-on:click="stream = !stream" :disabled="isBusy()"
                                    :aria-checked="stream"
                                    class="relative inline-flex h-5.5 w-10 shrink-0 items-center rounded-full transition disabled:opacity-60"
                                    :class="stream ? 'bg-brand-600' : 'bg-zinc-300 dark:bg-white/15'">
                                    <span class="inline-block size-4 translate-x-0.5 rounded-full bg-white shadow transition"
                                        :class="stream ? 'translate-x-[22px]' : 'translate-x-0.5'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Variables --}}
                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="flex items-center justify-between border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Variables</h2>
                        <span class="text-xs tabular-nums text-zinc-400 dark:text-zinc-500" x-text="variables.length + ' defined'"></span>
                    </header>

                    <div class="space-y-3 p-4 sm:p-5">
                        <template x-if="variables.length === 0">
                            <div class="rounded-lg border border-dashed border-zinc-300 px-4 py-6 text-center dark:border-white/15">
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">No variables yet. Wrap text in &#123;&#123;braces&#125;&#125; in your message, or add one here.</p>
                                <flux:button variant="subtle" size="sm" icon="plus" class="mt-3" x-on:click="showAddVariable = true">Add variable</flux:button>
                            </div>
                        </template>

                        <template x-for="(v, i) in variables" :key="v.key">
                            <div class="rounded-lg border border-zinc-200 p-3 dark:border-white/10">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-mono text-[0.7rem] text-brand-600 dark:text-brand-400" x-text="tokenFor(v.key)"></span>
                                    <div class="flex items-center gap-1">
                                        <button type="button" x-on:click="insertVariable(v.key)"
                                            class="inline-flex size-6 items-center justify-center rounded-md text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-white/10 dark:hover:text-zinc-200" title="Insert in message">
                                            <flux:icon.plus class="size-3.5" />
                                        </button>
                                        <button type="button" x-on:click="removeVariable(v.key)"
                                            class="inline-flex size-6 items-center justify-center rounded-md text-zinc-400 transition hover:bg-zinc-100 hover:text-red-600 dark:hover:bg-white/10" title="Remove variable">
                                            <flux:icon.trash class="size-3.5" />
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400" x-text="v.label"></p>
                                <input :id="'var-' + i" type="text" x-model="v.value" :disabled="isBusy()" :placeholder="'value for ' + v.key"
                                    class="mt-2 block w-full rounded-md border-0 bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-800 ring-1 ring-inset ring-zinc-200 transition focus:ring-2 focus:ring-brand-500/60 disabled:opacity-60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10" />
                            </div>
                        </template>

                        <template x-if="variables.length > 0">
                            <div>
                                <flux:button variant="subtle" size="sm" icon="plus" x-show="!showAddVariable" x-on:click="showAddVariable = true" class="w-full justify-center">Add variable</flux:button>
                            </div>
                        </template>

                        <div x-show="showAddVariable" x-cloak class="space-y-2 rounded-lg border border-brand-500/30 bg-brand-500/[0.03] p-3">
                            <input x-model="newVariableKey" x-on:keydown.enter.prevent="addVariable()" placeholder="key (e.g. audience)"
                                class="block w-full rounded-md border-0 bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-800 ring-1 ring-inset ring-zinc-200 transition placeholder:font-sans focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10" />
                            <input x-model="newVariableLabel" x-on:keydown.enter.prevent="addVariable()" placeholder="label (e.g. Target audience)"
                                class="block w-full rounded-md border-0 bg-zinc-50 px-2.5 py-1.5 text-xs text-zinc-800 ring-1 ring-inset ring-zinc-200 transition placeholder:text-zinc-400 focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10" />
                            <div class="flex items-center gap-2">
                                <flux:button variant="primary" size="sm" x-on:click="addVariable()">Add</flux:button>
                                <flux:button variant="subtle" size="sm" x-on:click="showAddVariable = false">Cancel</flux:button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app.page-container>
</x-layouts.app>
