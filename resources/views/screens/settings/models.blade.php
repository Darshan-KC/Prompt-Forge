@php
    $models = \App\Support\MockData::models();
    $providers = \App\Support\MockData::providers();
@endphp
<div class="space-y-6">
    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="flex flex-col gap-3 border-b border-zinc-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/5">
            <div>
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Available models</h2>
                <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">{{ $models->count() }} models across {{ count($providers) }} providers.</p>
            </div>
            <flux:select size="sm" class="w-full sm:w-auto">
                <flux:select.option value="all">All providers</flux:select.option>
                @foreach ($providers as $provider)
                    <flux:select.option value="{{ $provider['slug'] }}">{{ $provider['name'] }}</flux:select.option>
                @endforeach
            </flux:select>
        </header>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead>
                    <tr class="border-b border-zinc-100 text-[0.7rem] font-semibold uppercase tracking-wider text-zinc-400 dark:border-white/5 dark:text-zinc-500">
                        <th class="px-5 py-3 font-semibold">Model</th>
                        <th class="px-5 py-3 font-semibold">Context</th>
                        <th class="hidden px-5 py-3 font-semibold lg:table-cell">Pricing / 1M</th>
                        <th class="hidden px-5 py-3 font-semibold md:table-cell">Capabilities</th>
                        <th class="px-5 py-3 text-right font-semibold">Enabled</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @foreach ($models as $model)
                        <tr class="transition hover:bg-zinc-800/[0.02] dark:hover:bg-white/[0.02]">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $model['name'] }}</p>
                                <p class="font-mono text-xs text-zinc-400 dark:text-zinc-500">{{ $model['slug'] }}</p>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-xs tabular-nums text-zinc-600 dark:text-zinc-300">{{ number_format($model['context']) }}</td>
                            <td class="hidden px-5 py-3.5 lg:table-cell">
                                <p class="font-mono text-xs text-zinc-600 dark:text-zinc-300">${{ number_format($model['pricing']['input'], 2) }}/${{ number_format($model['pricing']['output'], 2) }}</p>
                            </td>
                            <td class="hidden px-5 py-3.5 md:table-cell">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($model['supports'] as $key => $value)
                                        @if ($value)
                                            <span class="rounded bg-zinc-100 px-1.5 py-0.5 font-mono text-[0.6rem] uppercase tracking-wide text-zinc-500 dark:bg-white/5 dark:text-zinc-400">{{ $key }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <button type="button" role="switch" aria-label="Toggle {{ $model['name'] }}" aria-checked="true" x-data="{ on: true }" x-on:click="on = !on" :aria-checked="on"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition"
                                    :class="on ? 'bg-brand-600' : 'bg-zinc-300 dark:bg-white/15'">
                                    <span class="inline-block size-3.5 translate-x-0.5 rounded-full bg-white shadow transition" :class="on ? 'translate-x-[20px]' : 'translate-x-0.5'"></span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>