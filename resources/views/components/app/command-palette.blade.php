@php
    $actions = [
        ['id' => 'act-new-prompt', 'type' => 'action', 'label' => 'New prompt', 'hint' => 'Create and iterate on a prompt', 'keywords' => 'new prompt create editor draft', 'route' => route('prompts.create'), 'icon' => 'plus'],
        ['id' => 'act-playground', 'type' => 'action', 'label' => 'Open playground', 'hint' => 'Test prompts against models', 'keywords' => 'playground run test execute compare', 'route' => route('playground'), 'icon' => 'bolt'],
        ['id' => 'act-browse', 'type' => 'action', 'label' => 'Browse prompts', 'hint' => 'Prompt library', 'keywords' => 'library prompts list search library', 'route' => route('prompts.index'), 'icon' => 'squares-2x2'],
        ['id' => 'act-history', 'type' => 'action', 'label' => 'View run history', 'hint' => 'Recent executions', 'keywords' => 'history runs logs execution tokens', 'route' => route('history.index'), 'icon' => 'clock'],
        ['id' => 'act-analytics', 'type' => 'action', 'label' => 'Open analytics', 'hint' => 'Usage, tokens and cost', 'keywords' => 'analytics usage cost tokens charts metrics', 'route' => route('analytics'), 'icon' => 'chart-bar'],
        ['id' => 'act-settings', 'type' => 'action', 'label' => 'Open settings', 'hint' => 'Profile, providers and preferences', 'keywords' => 'settings profile preferences appearance keys', 'route' => route('settings.profile'), 'icon' => 'cog-6-tooth'],
        ['id' => 'act-theme', 'type' => 'theme', 'label' => 'Toggle theme', 'hint' => null, 'keywords' => 'theme dark light mode switch appearance', 'route' => null, 'icon' => 'sun'],
    ];

    $promptActions = collect(\App\Support\MockData::prompts())
        ->map(fn (array $prompt) => [
            'id' => 'prompt-'.$prompt['id'],
            'type' => 'prompt',
            'label' => $prompt['name'],
            'hint' => $prompt['category'],
            'keywords' => strtolower($prompt['category'].' '.implode(' ', $prompt['tags']).' '.$prompt['slug']),
            'route' => route('prompts.show', $prompt['id']),
            'icon' => 'document-text',
        ])
        ->values()
        ->all();

    $paletteItems = array_merge($actions, $promptActions);
@endphp

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('commandPalette', () => ({
            open: false,
            query: '',
            index: 0,
            items: {{ Js::from($paletteItems) }},

            get results() {
                const terms = this.query.toLowerCase().split(' ').filter(Boolean);
                if (terms.length === 0) return this.items;
                return this.items.filter((item) => {
                    const haystack = (item.label + ' ' + (item.hint ?? '') + ' ' + (item.keywords ?? '')).toLowerCase();
                    return terms.every((term) => haystack.includes(term));
                });
            },

            select(current) {
                if (!current) return;
                this.open = false;
                if (current.type === 'theme') {
                    window.pfTheme?.toggle();
                    return;
                }
                if (current.route) {
                    window.location.href = current.route;
                }
            },

            openPalette() {
                this.query = '';
                this.index = 0;
                this.open = true;
                this.$nextTick(() => this.$refs.input?.focus());
            },

            closePalette() {
                this.open = false;
            },

            move(delta) {
                if (this.results.length === 0) return;
                this.index = (this.index + delta + this.results.length) % this.results.length;
            },
        }));
    });
</script>

<div
    x-data="commandPalette"
    x-on:keydown.cmd-k.window.prevent="openPalette()"
    x-on:keydown.ctrl-k.window.prevent="openPalette()"
    x-on:keydown.escape.window="closePalette()"
    x-on:open-command-palette.window="openPalette()"
    x-init="$watch('open', (value) => value && this.$nextTick(() => this.$refs.input?.focus()))"
>
    <template x-teleport="body">
        <div
            x-cloak
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center px-4 pt-[12vh]"
            role="dialog"
            aria-modal="true"
            aria-label="Command palette"
        >
            <div
                class="absolute inset-0 bg-zinc-950/50 backdrop-blur-sm"
                x-on:click="closePalette()"
                x-transition.opacity
            ></div>

            <div
                class="relative w-full max-w-xl overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xl shadow-zinc-950/20 animate-scale-in dark:border-white/10 dark:bg-zinc-900"
                x-on:click.outside="closePalette()"
            >
                <div class="flex items-center gap-3 border-b border-zinc-200 px-4 dark:border-white/10">
                    <flux:icon.magnifying-glass class="size-4 shrink-0 text-zinc-400" />
                    <input
                        x-ref="input"
                        type="text"
                        x-model="query"
                        x-on:keydown.arrow-down.prevent="move(1)"
                        x-on:keydown.arrow-up.prevent="move(-1)"
                        x-on:keydown.enter.prevent="select(results[index])"
                        x-on:keydown.escape.stop="closePalette()"
                        placeholder="Search prompts, projects and actions…"
                        aria-label="Search prompts, projects and actions"
                        class="h-12 w-full bg-transparent text-sm text-zinc-900 placeholder-zinc-400 outline-none dark:text-zinc-100 dark:placeholder-zinc-500"
                    />
                    <kbd class="hidden shrink-0 items-center rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 font-mono text-[0.65rem] text-zinc-400 sm:flex dark:border-white/10 dark:bg-white/5 dark:text-zinc-500">esc</kbd>
                </div>

                <div class="max-h-[16rem] overflow-y-auto p-2" role="listbox" aria-label="Results">
                    <template x-if="results.length === 0">
                        <div class="px-3 py-8 text-center">
                            <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">No results for “<span x-text="query"></span>”</p>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Try a different keyword, or create a new prompt.</p>
                        </div>
                    </template>

                    <template x-for="(item, i) in results" :key="item.id">
                        <button
                            type="button"
                            role="option"
                            x-bind:aria-selected="i === index"
                            x-on:click="select(item)"
                            x-on:mousemove="if (index !== i) index = i"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-start transition sm:gap-3"
                            x-bind:class="index === i ? 'bg-brand-500/10 dark:bg-white/10' : ''"
                        >
                            <span
                                class="grid size-7 shrink-0 place-items-center rounded-md bg-zinc-100 text-zinc-500 dark:bg-white/5 dark:text-zinc-400"
                                x-bind:class="index === i ? 'bg-brand-500/15 text-brand-700 dark:text-brand-300' : ''"
                            >
                                <flux:icon x-bind:icon="item.icon" class="size-4" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium text-zinc-800 dark:text-zinc-100" x-text="item.label"></span>
                                <span class="mt-0.5 block truncate text-xs text-zinc-500 dark:text-zinc-400">
                                    <span x-show="item.type === 'prompt'" class="font-medium text-zinc-400 dark:text-zinc-500">Prompt · </span>
                                    <span x-text="item.hint ?? ''"></span>
                                </span>
                            </span>
                            <kbd x-show="index === i" class="shrink-0 rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 font-mono text-[0.6rem] text-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-zinc-500">↵</kbd>
                        </button>
                    </template>
                </div>

                <div class="flex items-center gap-3 border-t border-zinc-200 bg-zinc-50/60 px-4 py-2 text-[0.65rem] text-zinc-400 dark:border-white/10 dark:bg-white/[0.03] dark:text-zinc-500">
                    <span class="flex items-center gap-1"><kbd class="rounded border border-zinc-200 px-1 font-mono dark:border-white/10">↑</kbd><kbd class="rounded border border-zinc-200 px-1 font-mono dark:border-white/10">↓</kbd> navigate</span>
                    <span class="flex items-center gap-1"><kbd class="rounded border border-zinc-200 px-1 font-mono dark:border-white/10">↵</kbd> select</span>
                    <span class="flex items-center gap-1"><kbd class="rounded border border-zinc-200 px-1 font-mono dark:border-white/10">esc</kbd> close</span>
                    <span class="ms-auto font-medium text-zinc-400 dark:text-zinc-500">prompt-forge</span>
                </div>
            </div>
        </div>
    </template>
</div>