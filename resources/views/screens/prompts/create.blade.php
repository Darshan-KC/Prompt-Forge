<x-layouts.app>
@php
    $providers = \App\Support\MockData::providers();
    $categories = \App\Support\MockData::categories();
    $folders = \App\Support\MockData::folders();
@endphp

<x-app.page-container class="space-y-6">
    <x-shared.page-header eyebrow="Prompt library" title="New prompt" description="Build a reusable prompt with system instructions, message, and typed variables.">
        <x-slot:actions>
            <flux:button href="{{ route('prompts.index') }}" wire:navigate variant="subtle">Cancel</flux:button>
        </x-slot:actions>
    </x-shared.page-header>

    <form x-data="{
        providers: {{ Js::from($providers) }},
        provider: 'anthropic',
        model: 'claude-3-7-sonnet',
        name: '',
        description: '',
        system: '',
        message: '',
        tags: [],
        tagInput: '',
        category: 'Development',
        folder: 1,
        temperature: 0.2,
        topP: 1,
        maxTokens: 2048,
        draft: false,
        variables: [],
        varKey: '',
        varLabel: '',
        showVarForm: false,

        tokenLabel(key) {
            return String.fromCharCode(123, 123) + key + String.fromCharCode(125, 125);
        },
        get models() {
            const prov = this.providers.find(p => p.slug === this.provider);
            return (prov ? prov.models : []).map(m => ({ ...m }));
        },
        get currentModel() { return this.models.find(m => m.slug === this.model) || {}; },
        get tokenCount() { return Math.max(1, Math.ceil((this.system.length + this.message.length) / 4)); },

        switchProvider(slug) {
            this.provider = slug;
            const list = this.models.map(m => m.slug);
            if (!list.includes(this.model) && list.length) this.model = list[0];
        },
        addTag() {
            const t = this.tagInput.trim().replace(/,/g, '');
            if (t && !this.tags.includes(t)) this.tags.push(t);
            this.tagInput = '';
        },
        removeTag(t) { this.tags = this.tags.filter(x => x !== t); },
        insertVariable(key) {
            this.message += this.tokenLabel(key);
            this.$nextTick(() => this.focusMessage());
        },
        focusMessage() { this.$refs.message.focus(); },
        addVariable() {
            const key = this.varKey.trim().replace(/[^a-zA-Z0-9_.]/g, '_').toLowerCase();
            if (!key) return;
            if (!this.variables.some(v => v.key === key)) this.variables.push({ key, label: this.varLabel.trim() || key });
            this.varKey = ''; this.varLabel = ''; this.showVarForm = false;
        },
        removeVariable(key) { this.variables = this.variables.filter(v => v.key !== key); },

        save() {
            if (!this.name.trim()) { this.$refs.name.focus(); return; }
            window.dispatchEvent(new CustomEvent('pf-toast', {
                detail: { icon: 'check-circle', message: 'Prompt created (mock) — persists once the backend is wired.' },
            }));
        }
    }" x-on:submit.prevent="save()" x-cloak class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        {{-- Left: editor --}}
        <div class="min-w-0 space-y-6">
            <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                <header class="border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Details</h2>
                </header>
                <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    <div class="sm:col-span-2">
                        <flux:field>
                            <flux:label>Name</flux:label>
                            <flux:input x-ref="name" x-model="name" placeholder="e.g. Code Review Assistant" required />
                        </flux:field>
                    </div>
                    <div class="sm:col-span-2">
                        <flux:field>
                            <flux:label>Description</flux:label>
                            <flux:textarea x-model="description" rows="2" placeholder="What this prompt does, and when to use it…" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>Category</flux:label>
                        <flux:select x-model="category">
                            @foreach ($categories as $category)
                                <flux:select.option value="{{ $category }}">{{ $category }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                    <flux:field>
                        <flux:label>Folder</flux:label>
                        <flux:select x-model.number="folder">
                            <flux:select.option :value="null">Uncategorized</flux:select.option>
                            @foreach ($folders as $folder)
                                <flux:select.option value="{{ $folder['id'] }}">{{ $folder['name'] }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                    <div class="sm:col-span-2">
                        <flux:field>
                            <flux:label>Tags</flux:label>
                            <div class="relative">
                                <flux:input x-model="tagInput" x-on:keydown.enter.prevent="addTag()" x-on:keydown.comma.prevent="addTag()" placeholder="Type a tag and press Enter…" />
                            </div>
                            <div class="mt-2 flex flex-wrap gap-1.5" x-show="tags.length > 0">
                                <template x-for="t in tags" :key="t">
                                    <span class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2 py-0.5 font-mono text-xs text-zinc-600 dark:bg-white/5 dark:text-zinc-300">
                                        <span x-text="t"></span>
                                        <button type="button" x-on:click="removeTag(t)" aria-label="Remove tag" class="text-zinc-400 transition hover:text-red-500">
                                            <flux:icon.x-mark class="size-3" />
                                        </button>
                                    </span>
                                </template>
                            </div>
                        </flux:field>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                <header class="flex items-center justify-between border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Instructions</h2>
                    <span class="text-xs tabular-nums text-zinc-400 dark:text-zinc-500" x-text="tokenCount.toLocaleString() + ' ~tokens'"></span>
                </header>
                <div class="space-y-4 p-4 sm:p-5">
                    <flux:field>
                        <flux:label>System</flux:label>
                        <flux:textarea x-model="system" rows="5" class="font-mono text-sm leading-relaxed"
                            placeholder="Define the role, constraints, output format and tone of the model…" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Message</flux:label>
                        <flux:textarea x-ref="message" x-model="message" rows="8" class="font-mono text-sm leading-relaxed"
                            placeholder="Wrap dynamic values in &#123;&#123;curly braces&#125;&#125; — they become variables you can declare below..." />
                    </flux:field>

                    <div class="flex flex-wrap items-center gap-1.5" x-show="variables.length > 0" x-cloak>
                        <span class="text-xs text-zinc-400 dark:text-zinc-500">Insert variable:</span>
                        <template x-for="v in variables" :key="v.key">
                            <button type="button" x-on:click="insertVariable(v.key)"
                                class="inline-flex items-center gap-1 rounded-md border border-brand-500/20 bg-brand-500/5 px-2 py-0.5 font-mono text-xs text-brand-700 transition hover:bg-brand-500/10 dark:text-brand-300"
                                x-text="tokenLabel(v.key)"></button>
                        </template>
                    </div>
                </div>
            </section>
        </div>

        {{-- Right: variables + model wiring + actions --}}
        <div class="min-w-0 space-y-6">
            <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                <header class="flex items-center justify-between border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Variables</h2>
                    <span class="text-xs tabular-nums text-zinc-400 dark:text-zinc-500" x-text="variables.length + ' defined'"></span>
                </header>
                <div class="space-y-3 p-4 sm:p-5">
                    <template x-if="variables.length === 0">
                        <p class="rounded-lg border border-dashed border-zinc-300 px-4 py-5 text-center text-xs text-zinc-400 dark:border-white/15">
                            No variables yet. Declare one below, then insert it into your message.
                        </p>
                    </template>

                    <template x-for="(v, i) in variables" :key="v.key">
                        <div class="rounded-lg border border-zinc-200 p-3 dark:border-white/10">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-[0.7rem] font-medium text-brand-600 dark:text-brand-400" x-text="tokenLabel(v.key)"></span>
                                <button type="button" x-on:click="removeVariable(v.key)" class="text-zinc-400 transition hover:text-red-500" aria-label="Remove variable">
                                    <flux:icon.trash class="size-3.5" />
                                </button>
                            </div>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400" x-text="v.label"></p>
                        </div>
                    </template>

                    <template x-if="!showVarForm">
                        <flux:button variant="subtle" size="sm" icon="plus" type="button" x-on:click="showVarForm = true" class="w-full justify-center">Add variable</flux:button>
                    </template>

                    <div x-show="showVarForm" x-cloak class="space-y-2 rounded-lg border border-brand-500/30 bg-brand-500/[0.03] p-3">
                        <input x-model="varKey" x-on:keydown.enter.prevent="addVariable()" placeholder="key (e.g. audience)"
                            class="block w-full rounded-md border-0 bg-zinc-50 px-2.5 py-1.5 font-mono text-xs text-zinc-800 ring-1 ring-inset ring-zinc-200 transition placeholder:font-sans focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10" />
                        <input x-model="varLabel" x-on:keydown.enter.prevent="addVariable()" placeholder="label (e.g. Target audience)"
                            class="block w-full rounded-md border-0 bg-zinc-50 px-2.5 py-1.5 text-xs text-zinc-800 ring-1 ring-inset ring-zinc-200 transition placeholder:text-zinc-400 focus:ring-2 focus:ring-brand-500/60 dark:bg-zinc-950/40 dark:text-zinc-200 dark:ring-white/10" />
                        <div class="flex items-center gap-2">
                            <flux:button variant="primary" size="sm" type="button" x-on:click="addVariable()">Add</flux:button>
                            <flux:button variant="subtle" size="sm" type="button" x-on:click="showVarForm = false">Cancel</flux:button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                <header class="border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Model defaults</h2>
                </header>
                <div class="space-y-4 p-4 sm:p-5">
                    <div>
                        <label class="text-xs font-medium text-zinc-400">Provider</label>
                        <div class="mt-2 grid grid-cols-1 gap-1.5">
                            <template x-for="p in providers" :key="p.slug">
                                <button type="button" x-on:click="switchProvider(p.slug)"
                                    class="flex items-center gap-2.5 rounded-lg border px-3 py-2 text-left text-sm transition"
                                    :class="provider === p.slug ? 'border-brand-500/50 bg-brand-500/5 ring-1 ring-brand-500/20' : 'border-zinc-200 hover:border-zinc-300 dark:border-white/10 dark:hover:border-white/15'">
                                    <span class="size-2 shrink-0 rounded-full" :style="'background-color:' + p.color"></span>
                                    <span class="min-w-0 flex-1 truncate font-medium text-zinc-700 dark:text-zinc-200" x-text="p.name"></span>
                                    <flux:icon.check class="size-3.5 text-brand-600" x-show="provider === p.slug" />
                                </button>
                            </template>
                        </div>
                    </div>

                    <flux:field>
                        <flux:label>Default model</flux:label>
                        <flux:select x-model="model">
                            <flux:select.option value="">— pick a model —</flux:select.option>
                            <template x-for="m in models" :key="m.slug">
                                <option x-bind:value="m.slug" x-text="m.name + ' (' + m.slug + ')'"></option>
                            </template>
                        </flux:select>
                    </flux:field>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="create-temp" class="text-xs font-medium text-zinc-400">Temperature</label>
                            <span class="font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300" x-text="temperature.toFixed(2)"></span>
                        </div>
                        <input id="create-temp" type="range" min="0" max="2" step="0.05" x-model.number="temperature" class="mt-2 w-full cursor-pointer accent-brand-600" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="create-topp" class="text-xs font-medium text-zinc-400">Top P</label>
                            <span class="font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300" x-text="topP.toFixed(2)"></span>
                        </div>
                        <input id="create-topp" type="range" min="0" max="1" step="0.05" x-model.number="topP" class="mt-2 w-full cursor-pointer accent-brand-600" />
                    </div>

                    <flux:field>
                        <flux:label>Max tokens</flux:label>
                        <flux:input type="number" min="0" step="256" x-model.number="maxTokens" class="font-mono" />
                    </flux:field>
                </div>
            </section>

            <section class="rounded-xl border border-zinc-200 bg-white p-4 shadow-xs sm:p-5 dark:border-white/10 dark:bg-zinc-900/60">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">Save as draft</p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">Keep it private until you publish it.</p>
                    </div>
                    <button type="button" role="switch" x-on:click="draft = !draft" :aria-checked="draft"
                        class="relative inline-flex h-5.5 w-10 shrink-0 items-center rounded-full transition"
                        :class="draft ? 'bg-brand-600' : 'bg-zinc-300 dark:bg-white/15'">
                        <span class="inline-block size-4 translate-x-0.5 rounded-full bg-white shadow transition"
                            :class="draft ? 'translate-x-[22px]' : 'translate-x-0.5'"></span>
                    </button>
                </div>
                <div class="flex flex-col gap-2">
                    <flux:button variant="primary" type="submit" icon="check" class="w-full justify-center">Create prompt</flux:button>
                    <flux:link href="{{ route('prompts.index') }}" wire:navigate variant="subtle" class="justify-center">Cancel</flux:link>
                </div>
            </section>
        </div>
    </form>
</x-app.page-container>
</x-layouts.app>
