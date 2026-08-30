<div class="space-y-6">
    <section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
        <header class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-white/5">
            <div>
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">API keys</h2>
                <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">Keys for the Prompt-Forge API — not provider keys (those live under AI providers).</p>
            </div>
        </header>

        <div class="p-5">
            <form x-data="{ name: '', created: null, key: '' }"
                x-on:submit.prevent="
                    if (!name.trim()) return;
                    const bytes = new Uint8Array(24);
                    crypto.getRandomValues(bytes);
                    key = 'pf_' + Array.from(bytes, b => b.toString(16).padStart(2, '0')).join('');
                    created = name.trim();
                    $dispatch('pf-toast', { icon: 'check-circle', title: 'API key created', message: 'Copy it now — it will not be shown again.' });
                "
                class="mb-5 space-y-3 rounded-lg border border-zinc-200 p-4 dark:border-white/10">
                <div class="flex flex-col gap-2 sm:flex-row">
                    <flux:input x-model="name" placeholder="Key name (e.g. CI pipeline)" class="flex-1" />
                    <flux:button variant="primary" type="submit" icon="plus" class="sm:w-auto">Create key</flux:button>
                </div>
                <template x-if="key">
                    <div class="flex items-center gap-2 rounded-md bg-zinc-900 px-3 py-2 dark:bg-white/5">
                        <code class="min-w-0 flex-1 truncate font-mono text-xs text-brand-300" x-text="key"></code>
                        <button type="button" x-data="{ copied: false }" x-on:click="navigator.clipboard.writeText(key); copied = true; setTimeout(() => copied = false, 1200)"
                            class="shrink-0 inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-xs font-medium text-zinc-300 hover:text-white"
                            x-text="copied ? 'Copied ✓' : 'Copy'"></button>
                    </div>
                </template>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 text-[0.7rem] font-semibold uppercase tracking-wider text-zinc-400 dark:border-white/5 dark:text-zinc-500">
                            <th class="py-2.5 font-semibold">Name</th>
                            <th class="py-2.5 font-semibold">Key</th>
                            <th class="hidden py-2.5 font-semibold sm:table-cell">Last used</th>
                            <th class="py-2.5 text-right font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                        @foreach ([['ci-pipeline', 'pf_a1b2…f9e8', '3m ago'], ['prod-runs', 'pf_9c4d…0b2a', '2h ago']] as [$name, $masked, $used])
                            <tr class="transition hover:bg-zinc-800/[0.02] dark:hover:bg-white/[0.02]">
                                <td class="py-3">
                                    <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $name }}</p>
                                </td>
                                <td class="align-middle">
                                    <code class="rounded bg-zinc-100 px-2 py-0.5 font-mono text-xs text-zinc-500 dark:bg-white/5 dark:text-zinc-400">{{ $masked }}</code>
                                </td>
                                <td class="hidden py-3 text-xs text-zinc-400 sm:table-cell dark:text-zinc-500">{{ $used }}</td>
                                <td class="py-3 text-right">
                                    <flux:button variant="danger" size="sm" x-data x-on:click="$dispatch('pf-toast', { icon: 'exclamation-triangle', title: 'Revoked', message: 'Key revocation is mocked.' })">Revoke</flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>