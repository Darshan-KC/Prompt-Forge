@php
    $actions = [
        [
            'id' => 'act-new-prompt',
            'type' => 'action',
            'label' => 'New prompt',
            'hint' => 'Create and iterate on a prompt',
            'keywords' => 'new prompt create editor draft',
            'route' => route('prompts.create'),
            'icon' => 'plus',
        ],
        [
            'id' => 'act-playground',
            'type' => 'action',
            'label' => 'Open playground',
            'hint' => 'Test prompts against models',
            'keywords' => 'playground run test execute compare',
            'route' => route('playground'),
            'icon' => 'bolt',
        ],
        [
            'id' => 'act-browse',
            'type' => 'action',
            'label' => 'Browse prompts',
            'hint' => 'Prompt library',
            'keywords' => 'library prompts list search library',
            'route' => route('prompts.index'),
            'icon' => 'squares-2x2',
        ],
        [
            'id' => 'act-history',
            'type' => 'action',
            'label' => 'View run history',
            'hint' => 'Recent executions',
            'keywords' => 'history runs logs execution tokens',
            'route' => route('history.index'),
            'icon' => 'clock',
        ],
        [
            'id' => 'act-analytics',
            'type' => 'action',
            'label' => 'Open analytics',
            'hint' => 'Usage, tokens and cost',
            'keywords' => 'analytics usage cost tokens charts metrics',
            'route' => route('analytics'),
            'icon' => 'chart-bar',
        ],
        [
            'id' => 'act-settings',
            'type' => 'action',
            'label' => 'Open settings',
            'hint' => 'Profile, providers and preferences',
            'keywords' => 'settings profile preferences appearance keys',
            'route' => route('settings.profile'),
            'icon' => 'cog-6-tooth',
        ],
        [
            'id' => 'act-theme',
            'type' => 'theme',
            'label' => 'Toggle theme',
            'hint' => null,
            'keywords' => 'theme dark light mode switch appearance',
            'route' => null,
            'icon' => 'sun',
        ],
    ];

    $promptActions = collect(\App\Support\MockData::prompts())
        ->map(fn (array $prompt) => [
            'id' => 'prompt-'.$prompt['id'],
            'type' => 'prompt',
            'label' => $prompt['name'],
            'hint' => $prompt['category'],
            'keywords' => strtolower(
                $prompt['category'].' '.
                implode(' ', $prompt['tags']).' '.
                $prompt['slug']
            ),
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

            icons: {
                plus: `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                `,

                bolt: `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m3.75 13.5 10.5-11.25L12.75 10.5h7.5L9.75 21.75 11.25 13.5h-7.5Z" />
                    </svg>
                `,

                'squares-2x2': `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                `,

                clock: `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6l4 2.25m6-2.25a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" />
                    </svg>
                `,

                'chart-bar': `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 3v17.25h17.25M7.5 16.5v-6m4.5 6V6m4.5 10.5V9m4.5 7.5V3" />
                    </svg>
                `,

                'cog-6-tooth': `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.592c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.312.686.646.87.344.19.768.19 1.112 0l1.14-.653a1.125 1.125 0 0 1 1.394.14l1.834 1.834c.39.39.445 1.024.14 1.394l-.653 1.14c-.19.344-.19.768 0 1.112.184.334.496.583.87.646l1.281.213c.542.09.94.56.94 1.11v2.592c0 .55-.398 1.02-.94 1.11l-1.281.213a1.125 1.125 0 0 0-.87.646c-.19.344-.19.768 0 1.112l.653 1.14c.305.37.25 1.004-.14 1.394l-1.834 1.834a1.125 1.125 0 0 1-1.394.14l-1.14-.653a1.125 1.125 0 0 0-1.112 0 1.125 1.125 0 0 0-.646.87l-.213 1.281c-.09.542-.56.94-1.11.94h-2.592c-.55 0-1.02-.398-1.11-.94l-.213-1.281a1.125 1.125 0 0 0-.646-.87 1.125 1.125 0 0 0-1.112 0l-1.14.653a1.125 1.125 0 0 1-1.394-.14l-1.834-1.834a1.125 1.125 0 0 1-.14-1.394l.653-1.14a1.125 1.125 0 0 0 0-1.112 1.125 1.125 0 0 0-.87-.646l-1.281-.213a1.125 1.125 0 0 1-.94-1.11v-2.592c0-.55.398-1.02.94-1.11l1.281-.213a1.125 1.125 0 0 0 .87-.646c.19-.344.19-.768 0-1.112l-.653-1.14a1.125 1.125 0 0 1 .14-1.394l1.834-1.834a1.125 1.125 0 0 1 1.394-.14l1.14.653c.344.19.768.19 1.112 0 .334-.184.583-.496.646-.87l.213-1.281Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                `,

                sun: `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-1.977 4.773 1.591 1.591M12 18.75V21m-4.773-1.977-1.591 1.591M5.25 12H3m3.636-5.364L5.045 5.045M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                `,

                'document-text': `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3h5.25M9 3.375H5.625c-.621 0-1.125.504-1.125 1.125v15c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V9.75M9 3.375V9h5.625" />
                    </svg>
                `,
            },

            get results() {
                const terms = this.query
                    .toLowerCase()
                    .split(/\s+/)
                    .filter(Boolean);

                if (terms.length === 0) {
                    return this.items;
                }

                return this.items.filter((item) => {
                    const haystack = (
                        item.label + ' ' +
                        (item.hint ?? '') + ' ' +
                        (item.keywords ?? '')
                    ).toLowerCase();

                    return terms.every((term) =>
                        haystack.includes(term)
                    );
                });
            },

            select(current) {
                if (!current) {
                    return;
                }

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

                this.$nextTick(() => {
                    this.$refs.input?.focus();
                });
            },

            closePalette() {
                this.open = false;
            },

            move(delta) {
                if (this.results.length === 0) {
                    return;
                }

                this.index = (
                    this.index +
                    delta +
                    this.results.length
                ) % this.results.length;
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
    x-init="$watch('open', value => value && $nextTick(() => $refs.input?.focus()))"
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
                <div
                    class="flex items-center gap-3 border-b border-zinc-200 px-4 dark:border-white/10"
                >
                    <flux:icon.magnifying-glass
                        class="size-4 shrink-0 text-zinc-400"
                    />

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

                    <kbd
                        class="hidden shrink-0 items-center rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 font-mono text-[0.65rem] text-zinc-400 sm:flex dark:border-white/10 dark:bg-white/5 dark:text-zinc-500"
                    >
                        esc
                    </kbd>
                </div>

                <div
                    class="max-h-[16rem] overflow-y-auto p-2"
                    role="listbox"
                    aria-label="Results"
                >
                    <template x-if="results.length === 0">
                        <div class="px-3 py-8 text-center">
                            <p
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-200"
                            >
                                No results for “<span x-text="query"></span>”
                            </p>

                            <p
                                class="mt-1 text-xs text-zinc-500 dark:text-zinc-400"
                            >
                                Try a different keyword, or create a new prompt.
                            </p>
                        </div>
                    </template>

                    <template
                        x-for="(item, i) in results"
                        :key="item.id"
                    >
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
                                <span
                                    x-html="icons[item.icon] ?? ''"
                                    aria-hidden="true"
                                ></span>
                            </span>

                            <span class="min-w-0 flex-1">
                                <span
                                    class="block truncate text-sm font-medium text-zinc-800 dark:text-zinc-100"
                                    x-text="item.label"
                                ></span>

                                <span
                                    class="mt-0.5 block truncate text-xs text-zinc-500 dark:text-zinc-400"
                                >
                                    <span
                                        x-show="item.type === 'prompt'"
                                        class="font-medium text-zinc-400 dark:text-zinc-500"
                                    >
                                        Prompt ·
                                    </span>

                                    <span x-text="item.hint ?? ''"></span>
                                </span>
                            </span>

                            <kbd
                                x-show="index === i"
                                class="shrink-0 rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 font-mono text-[0.6rem] text-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-zinc-500"
                            >
                                ↵
                            </kbd>
                        </button>
                    </template>
                </div>

                <div
                    class="flex items-center gap-3 border-t border-zinc-200 bg-zinc-50/60 px-4 py-2 text-[0.65rem] text-zinc-400 dark:border-white/10 dark:bg-white/[0.03] dark:text-zinc-500"
                >
                    <span class="flex items-center gap-1">
                        <kbd class="rounded border border-zinc-200 px-1 font-mono dark:border-white/10">↑</kbd>
                        <kbd class="rounded border border-zinc-200 px-1 font-mono dark:border-white/10">↓</kbd>
                        navigate
                    </span>

                    <span class="flex items-center gap-1">
                        <kbd class="rounded border border-zinc-200 px-1 font-mono dark:border-white/10">↵</kbd>
                        select
                    </span>

                    <span class="flex items-center gap-1">
                        <kbd class="rounded border border-zinc-200 px-1 font-mono dark:border-white/10">esc</kbd>
                        close
                    </span>

                    <span class="ms-auto font-medium text-zinc-400 dark:text-zinc-500">
                        prompt-forge
                    </span>
                </div>
            </div>
        </div>
    </template>
</div>