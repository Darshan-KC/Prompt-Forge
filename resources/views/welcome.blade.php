<x-layouts.guest title="Prompt-Forge — The professional AI prompt engineering workspace">
    @php
        $features = [
            ['icon' => 'pencil-square', 'title' => 'A real prompt editor', 'body' => 'System and user instructions, variables, temperature, max tokens — structured like a serious engineering artifact, not a chat box.'],
            ['icon' => 'variable', 'title' => 'Variables, not one-offs', 'body' => 'Declare {{variables}} once, swap values anytime. Reuse the same prompt across audiences, codebases and languages.'],
            ['icon' => 'arrows-right-left', 'title' => 'Compare every model', 'body' => 'Run one prompt across GPT, Claude, Gemini and more, side by side. See tokens, latency and cost before you commit.'],
            ['icon' => 'clock', 'title' => 'Version everything', 'body' => 'Every tweak is a version. Restore, diff, and iterate without losing the experiment that got you there.'],
            ['icon' => 'squares-2x2', 'title' => 'Organize your library', 'body' => 'Folders, tags, projects and favorites. Your prompt library scales past the first hundred without collapsing.'],
            ['icon' => 'chart-bar', 'title' => 'Know what it costs', 'body' => 'Per-run token, latency and cost telemetry — and analytics that show which prompts earn their keep.'],
        ];

        $steps = [
            ['step' => '01', 'title' => 'Write', 'body' => 'Draft system instructions and a prompt with variables in a focused editor.'],
            ['step' => '02', 'title' => 'Run & compare', 'body' => 'Fire it against any model. Watch token, latency and cost as it streams.'],
            ['step' => '03', 'title' => 'Iterate & ship', 'body' => 'Save versions, diff what changed, and reuse proven prompts everywhere.'],
        ];

        $plans = [
            ['name' => 'Starter', 'price' => '$0', 'period' => '/month', 'tag' => null, 'features' => ['25 runs / day', '2 providers', 'Community support', '1 project'], 'cta' => 'Start for free', 'highlight' => false],
            ['name' => 'Pro', 'price' => '$19', 'period' => '/month', 'tag' => 'Most popular', 'features' => ['Unlimited runs', 'All providers & models', 'Version history + diffs', 'Unlimited projects', 'Priority support'], 'cta' => 'Go Pro', 'highlight' => true],
            ['name' => 'Team', 'price' => '$49', 'period' => '/month', 'tag' => null, 'features' => ['Everything in Pro', 'Shared prompt libraries', 'Usage & cost analytics', 'SSO (coming soon)'], 'cta' => 'Contact sales', 'highlight' => false],
        ];
    @endphp

    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-zinc-200/80 dark:border-white/10">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -top-40 left-1/2 h-[480px] w-[860px] -translate-x-1/2 rounded-full bg-brand-500/[0.07] blur-3xl dark:bg-brand-500/10"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(0,0,0,0.02)_1px,transparent_1px),linear-gradient(to_bottom,rgba(0,0,0,0.02)_1px,transparent_1px)] bg-[size:36px_36px] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.025)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.025)_1px,transparent_1px)] [mask-image:radial-gradient(ellipse_70%_50%_at_50%_0%,black,transparent)]"></div>
        </div>

        <x-app.page-container class="relative py-20 text-center sm:py-28">
            <p class="mx-auto inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/70 px-3 py-1 text-xs font-medium text-zinc-600 shadow-xs backdrop-blur dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                The professional prompt engineering workspace
            </p>

            <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-semibold leading-[1.1] tracking-tight text-zinc-900 sm:text-6xl dark:text-white">
                Iterate prompts like you'd write
                <span class="relative whitespace-nowrap text-brand-600 dark:text-brand-400">
                    production code<span class="animate-caret absolute -right-2 bottom-1 inline-block h-[0.6em] w-[3px] bg-brand-500" aria-hidden="true"></span>
                </span>
            </h1>

            <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-zinc-500 sm:text-lg dark:text-zinc-400">
                Prompt-Forge is the environment for building, versioning and measuring AI prompts —
                across every model, with full visibility into tokens, latency and cost.
            </p>

            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <flux:button variant="primary" size="lg" href="{{ route('register') }}" wire:navigate icon:trailing="arrow-right">
                    Start building — it's free
                </flux:button>
                <flux:button variant="subtle" size="lg" href="{{ route('login') }}" wire:navigate>
                    Log in to your workspace
                </flux:button>
            </div>

            <p class="mt-4 text-xs text-zinc-400 dark:text-zinc-500">Free forever · 25 runs/day · No credit card required</p>
        </x-app.page-container>
    </section>

    {{-- Provider strip --}}
    <section aria-label="Supported AI providers">
        <x-app.page-container class="py-10">
            <div class="flex flex-col items-center gap-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Connect once. Run everywhere.</p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    @foreach (\App\Support\MockData::providers() as $provider)
                        <span class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm font-medium text-zinc-600 shadow-xs dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                            <span class="size-2 rounded-full" style="background-color: {{ $provider['color'] }}" aria-hidden="true"></span>
                            {{ $provider['name'] }}
                        </span>
                    @endforeach
                    <span class="inline-flex items-center gap-2 rounded-lg border border-dashed border-zinc-300 px-3.5 py-2 text-sm font-medium text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                        + your own endpoint
                    </span>
                </div>
            </div>
        </x-app.page-container>
    </section>

    {{-- Product hero mock --}}
    <section class="relative overflow-hidden pb-20 sm:pb-24">
        <x-app.page-container>
            <div class="relative rounded-2xl border border-zinc-200 bg-white shadow-2xl shadow-zinc-950/10 dark:border-white/10 dark:bg-zinc-900">
                <div class="flex items-center gap-1.5 border-b border-zinc-200 px-4 py-3 dark:border-white/10">
                    <span class="size-2.5 rounded-full bg-zinc-200 dark:bg-white/15"></span>
                    <span class="size-2.5 rounded-full bg-zinc-200 dark:bg-white/15"></span>
                    <span class="size-2.5 rounded-full bg-zinc-200 dark:bg-white/15"></span>
                    <span class="ml-3 hidden rounded-md bg-zinc-100 px-2 py-0.5 font-mono text-[0.65rem] text-zinc-400 sm:block dark:bg-white/5 dark:text-zinc-500">prompt-forge.app/playground</span>
                </div>

                <div class="grid sm:grid-cols-2">
                    <div class="border-b border-zinc-200 p-5 sm:border-b-0 sm:border-r dark:border-white/10">
                        <p class="font-mono text-[0.65rem] font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">System</p>
                        <div class="mt-2 space-y-1.5">
                            <div class="h-2 w-11/12 rounded bg-zinc-100 dark:bg-white/10"></div>
                            <div class="h-2 w-3/5 rounded bg-zinc-100 dark:bg-white/10"></div>
                        </div>
                        <p class="mt-4 font-mono text-[0.65rem] font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">Prompt</p>
                        <div class="mt-2 space-y-1.5">
                            <div class="h-2 w-10/12 rounded bg-zinc-100 dark:bg-white/10"></div>
                            <div class="flex gap-1.5">
                                <span class="rounded bg-brand-500/10 px-1.5 py-0.5 font-mono text-[0.6rem] text-brand-700 dark:text-brand-300">{{ '{{' }}topic{{ '}}' }}</span>
                                <span class="h-2 w-24 self-center rounded bg-zinc-100 dark:bg-white/10"></span>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            <span class="inline-flex items-center gap-1.5 rounded-md border border-zinc-200 px-2 py-1 font-mono text-[0.6rem] text-zinc-500 dark:border-white/10 dark:text-zinc-400">
                                <span class="size-1.5 rounded-full bg-[#d97757]"></span> claude-3-7-sonnet
                            </span>
                            <span class="rounded-md border border-zinc-200 px-2 py-1 font-mono text-[0.6rem] text-zinc-500 dark:border-white/10 dark:text-zinc-400">0.2 · temp</span>
                            <span class="rounded-md border border-zinc-200 px-2 py-1 font-mono text-[0.6rem] text-zinc-500 dark:border-white/10 dark:text-zinc-400">4,096 · max</span>
                        </div>
                    </div>

                    <div class="bg-zinc-50/70 p-5 dark:bg-white/[0.02]">
                        <div class="flex items-center justify-between">
                            <p class="font-mono text-[0.65rem] font-semibold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Output</p>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[0.65rem] font-medium text-emerald-700 dark:text-emerald-300">
                                <span class="size-1 rounded-full bg-current"></span> streaming
                            </span>
                        </div>
                        <div class="mt-3 space-y-1.5">
                            <div class="h-2 w-full rounded bg-zinc-100 dark:bg-white/10"></div>
                            <div class="h-2 w-11/12 rounded bg-zinc-100 dark:bg-white/10"></div>
                            <div class="h-2 w-4/5 rounded bg-zinc-100 dark:bg-white/10"></div>
                            <div class="h-2 w-2/3 rounded bg-zinc-100 dark:bg-white/10"></div>
                        </div>
                        <div class="mt-4 flex items-center gap-4 rounded-lg border border-zinc-200 bg-white px-3 py-2 font-mono text-[0.65rem] text-zinc-500 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-400">
                            <span>Tokens <b class="font-semibold text-zinc-800 dark:text-zinc-200">824</b></span>
                            <span>Latency <b class="font-semibold text-zinc-800 dark:text-zinc-200">1.2s</b></span>
                            <span>Cost <b class="font-semibold text-zinc-800 dark:text-zinc-200">$0.012</b></span>
                        </div>
                    </div>
                </div>
            </div>
        </x-app.page-container>
    </section>

    {{-- Features --}}
    <section id="features" class="border-t border-zinc-200/80 py-20 sm:py-24 dark:border-white/10">
        <x-app.page-container>
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">Features</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">Built for engineers who prompt like they ship</h2>
                <p class="mt-3 text-base leading-relaxed text-zinc-500 dark:text-zinc-400">Prompt-Forge treats prompts as durable artifacts — versioned, measurable and reusable — not throwaway chat messages.</p>
            </div>

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($features as $feature)
                    <div class="group rounded-xl border border-zinc-200 bg-white p-6 shadow-xs transition hover:border-brand-500/40 hover:shadow-md dark:border-white/10 dark:bg-zinc-900/60 dark:hover:border-brand-400/30">
                        <div class="grid size-10 place-items-center rounded-lg bg-brand-500/10 text-brand-600 transition group-hover:bg-brand-500/15 dark:text-brand-400">
                            <flux:icon :icon="$feature['icon']" class="size-5" />
                        </div>
                        <h3 class="mt-4 text-[0.95rem] font-semibold text-zinc-900 dark:text-white">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $feature['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-app.page-container>
    </section>

    {{-- Workflow --}}
    <section id="workflow" class="border-t border-zinc-200/80 py-20 sm:py-24 dark:border-white/10">
        <x-app.page-container>
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">The loop</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">Write. Run. Compare. Ship.</h2>
            </div>

            <ol class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ($steps as $index => $step)
                    <li class="relative flex gap-4">
                        @if (! $loop->last)
                            <span class="absolute left-5 top-14 hidden h-[calc(100%-3rem)] w-px bg-gradient-to-b from-zinc-200 to-transparent md:block dark:from-white/10" aria-hidden="true"></span>
                        @endif
                        <div>
                            <div class="grid size-10 place-items-center rounded-full border border-zinc-200 bg-white font-mono text-xs font-semibold text-brand-600 shadow-xs dark:border-white/10 dark:bg-zinc-900 dark:text-brand-400">{{ $step['step'] }}</div>
                        </div>
                        <div>
                            <h3 class="text-[0.95rem] font-semibold text-zinc-900 dark:text-white">{{ $step['title'] }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $step['body'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </x-app.page-container>
    </section>

    {{-- Pricing --}}
    <section id="pricing" class="border-t border-zinc-200/80 py-20 sm:py-24 dark:border-white/10">
        <x-app.page-container>
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">Pricing</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">Start free. Scale when your prompts do.</h2>
                <p class="mt-3 text-base leading-relaxed text-zinc-500 dark:text-zinc-400">You pay for the workspace, not for margins on model tokens.</p>
            </div>

            <div class="mt-12 grid gap-5 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    <div class="relative flex flex-col rounded-2xl border p-6 shadow-xs {{ $plan['highlight'] ? 'border-brand-500/40 bg-gradient-to-b from-brand-500/[0.06] to-white shadow-lg shadow-brand-500/10 dark:border-brand-400/40 dark:from-brand-500/10 dark:to-zinc-900/70' : 'border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-900/60' }}">
                        @if ($plan['tag'])
                            <span class="absolute -top-3 left-6 rounded-full bg-brand-600 px-2.5 py-0.5 text-[0.65rem] font-semibold uppercase tracking-wider text-white dark:bg-brand-500 dark:text-brand-950">{{ $plan['tag'] }}</span>
                        @endif
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $plan['name'] }}</h3>
                        <div class="mt-3 flex items-baseline gap-1">
                            <span class="text-4xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $plan['price'] }}</span>
                            <span class="text-sm text-zinc-400 dark:text-zinc-500">{{ $plan['period'] }}</span>
                        </div>
                        <ul class="mt-6 space-y-2.5 text-sm">
                            @foreach ($plan['features'] as $featureItem)
                                <li class="flex items-start gap-2.5 text-zinc-600 dark:text-zinc-300">
                                    <flux:icon.check class="mt-0.5 size-4 shrink-0 text-brand-600 dark:text-brand-400" />
                                    {{ $featureItem }}
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-8 flex-1"></div>
                        <flux:button variant="{{ $plan['highlight'] ? 'primary' : 'subtle' }}" href="{{ route('register') }}" wire:navigate class="w-full justify-center">{{ $plan['cta'] }}</flux:button>
                    </div>
                @endforeach
            </div>
        </x-app.page-container>
    </section>

    {{-- CTA band --}}
    <section class="border-t border-zinc-200/80 pb-24 pt-16 dark:border-white/10">
        <x-app.page-container>
            <div class="relative overflow-hidden rounded-2xl bg-zinc-900 px-6 py-14 text-center dark:border dark:border-white/10 dark:bg-zinc-900/70 sm:px-12">
                <div class="pointer-events-none absolute -top-24 left-1/2 h-64 w-[560px] -translate-x-1/2 rounded-full bg-brand-500/20 blur-3xl" aria-hidden="true"></div>
                <div class="relative">
                    <h2 class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">Your best prompt deserves a proper workshop.</h2>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-zinc-400">Sign up in under a minute and run your first side-by-side model comparison today.</p>
                    <div class="mt-7 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <flux:button variant="primary" size="lg" href="{{ route('register') }}" wire:navigate icon:trailing="arrow-right">Create your workspace</flux:button>
                        <flux:link href="{{ route('login') }}" class="text-zinc-300">Already have an account?</flux:link>
                    </div>
                </div>
            </div>
        </x-app.page-container>
    </section>
</x-layouts.guest>