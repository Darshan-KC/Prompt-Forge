@php($providers = \App\Support\MockData::providers())
<div class="space-y-6">
    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-white/5">
            <div>
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Connected providers</h2>
                <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">API keys are stored encrypted and used only for your runs.</p>
            </div>
            <flux:button variant="primary" size="sm" icon="plus" x-data x-on:click="$dispatch('pf-toast', { title: 'Add provider', message: 'Adding providers is mocked until the backend is wired.' })">Add provider</flux:button>
        </header>
        <ul class="divide-y divide-zinc-100 dark:divide-white/5">
            @foreach ($providers as $provider)
                <li class="flex items-center gap-4 px-5 py-4" x-data x-on:click="$dispatch('pf-toast', { title: 'Simulated', message: 'Provider management is mocked.' })">
                    <span class="grid size-9 shrink-0 place-items-center rounded-lg text-xs font-bold text-white" style="background-color: {{ $provider['color'] }}">
                        {{ $provider['badge'] }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $provider['name'] }}</p>
                        <p class="font-mono text-xs text-zinc-400 dark:text-zinc-500">{{ $provider['tagline'] }} · {{ count($provider['models']) }} models</p>
                    </div>
                    <x-shared.status-badge :status="$provider['status']" :dot="false" />
                    <flux:icon.chevron-right class="size-4 shrink-0 text-zinc-300 dark:text-zinc-600" />
                </li>
            @endforeach
        </ul>
    </section>

    <section class="rounded-xl border border-dashed border-zinc-300 bg-white/50 p-6 dark:border-white/10 dark:bg-white/[0.02]">
        <div class="flex items-start gap-3">
            <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400">
                <flux:icon.server-stack class="size-4" />
            </span>
            <div class="min-w-0">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Bring your own endpoint</h3>
                <p class="mt-1 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                    Connect self-hosted or OpenAI-compatible endpoints (Ollama, vLLM, LM Studio…) with a custom base URL.
                    Provider CRUD arrives with the AI engineering backend.
                </p>
            </div>
        </div>
    </section>
</div>