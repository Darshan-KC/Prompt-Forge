@php
    $runId = is_numeric($run ?? null) ? (int) $run : null;
    $runData = $runId !== null && $runId > 0 ? \App\Support\MockData::run($runId) : null;
    $prompt = $runData ? \App\Support\MockData::prompt($runData['promptId']) : null;
@endphp

<x-app.page-container class="space-y-6">
    @if (! $runData)
        <div class="mx-auto max-w-sm py-20">
            <x-shared.empty-state icon="clock" title="Run not found" description="This run doesn't exist in the mock history.">
                <flux:button href="{{ route('history.index') }}" wire:navigate variant="primary" size="sm">Back to history</flux:button>
            </x-shared.empty-state>
        </div>
    @else
        <x-shared.page-header
            eyebrow="Run history"
            :title="$runData['promptName']"
            :description="'Run #'.$runData['id'].' · '. \Illuminate\Support\Carbon::parse($runData['createdAt'])->format('M j, Y \a\t g:i A')"
        >
            <x-slot:actions>
                @if ($prompt)
                    <flux:button variant="primary" icon="play" href="{{ route('playground', ['prompt' => $prompt['id']]) }}" wire:navigate>Rerun</flux:button>
                @endif
                <flux:link href="{{ route('history.index') }}" wire:navigate variant="subtle">← History</flux:link>
            </x-slot:actions>
        </x-shared.page-header>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <div class="min-w-0 space-y-6">
                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="flex items-center justify-between px-4 py-3 dark:border-white/5">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Output</h2>
                            <x-shared.status-badge :status="$runData['status']" />
                        </div>
                        <button type="button"
                            x-data="{ copied: false }"
                            x-on:click="navigator.clipboard.writeText(@js($runData['outputPreview'])); copied = true; setTimeout(() => copied = false, 1500)"
                            class="inline-flex items-center gap-1.5 rounded-md px-2 py-1.5 text-xs font-medium text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-zinc-200">
                            <flux:icon.clipboard-document x-show="!copied" class="size-3.5" />
                            <flux:icon.check x-show="copied" class="size-3.5 text-emerald-500" />
                            <span x-text="copied ? 'Copied' : 'Copy output'"></span>
                        </button>
                    </header>
                    <pre class="whitespace-pre-wrap p-5 font-mono text-sm leading-relaxed text-zinc-700 dark:text-zinc-300 max-h-[520px] overflow-auto">{{ $runData['outputPreview'] }}</pre>
                </section>
            </div>

            <aside class="min-w-0 space-y-6">
                <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
                    <header class="border-b border-zinc-100 px-4 py-3 dark:border-white/5">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Run details</h2>
                    </header>
                    <dl class="divide-y divide-zinc-100 px-4 py-1 text-sm dark:divide-white/5">
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-xs text-zinc-400 dark:text-zinc-500">Model</dt>
                            <dd><x-shared.model-badge :slug="$runData['model']" :provider-slug="$runData['provider']" /></dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-xs text-zinc-400 dark:text-zinc-500">Status</dt>
                            <dd><x-shared.status-badge :status="$runData['status']" :dot="false" /></dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-xs text-zinc-400 dark:text-zinc-500">Tokens in</dt>
                            <dd class="font-mono text-xs tabular-nums text-zinc-700 dark:text-zinc-300">{{ number_format($runData['tokensIn']) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-xs text-zinc-400 dark:text-zinc-500">Tokens out</dt>
                            <dd class="font-mono text-xs tabular-nums text-zinc-700 dark:text-zinc-300">{{ number_format($runData['tokensOut']) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-xs text-zinc-400 dark:text-zinc-500">Tokens total</dt>
                            <dd class="font-mono text-xs tabular-nums text-zinc-700 dark:text-zinc-300">{{ number_format($runData['tokens']) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-xs text-zinc-400 dark:text-zinc-500">Latency</dt>
                            <dd class="font-mono text-xs tabular-nums text-zinc-700 dark:text-zinc-300">{{ \App\Support\MockData::latency($runData['latencyMs']) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-xs text-zinc-400 dark:text-zinc-500">Cost</dt>
                            <dd class="font-mono text-xs tabular-nums text-zinc-700 dark:text-zinc-300">{{ $runData['cost'] > 0 ? \App\Support\MockData::money($runData['cost']) : '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-2.5">
                            <dt class="text-xs text-zinc-400 dark:text-zinc-500">Executed</dt>
                            <dd class="text-xs text-zinc-700 dark:text-zinc-300">{{ \Illuminate\Support\Carbon::parse($runData['createdAt'])->format('g:i A') }}</dd>
                        </div>
                    </dl>
                    @if ($prompt)
                        <div class="px-4 pb-4">
                            <flux:button href="{{ route('prompts.show', $prompt['id']) }}" wire:navigate variant="subtle" size="sm" icon="pencil-square" class="w-full justify-center">Open prompt</flux:button>
                        </div>
                    @endif
                </section>
            </aside>
        </div>
    @endif
</x-app.page-container>