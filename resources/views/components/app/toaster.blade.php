<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pfToasts', () => ({
            toasts: [],
            counter: 0,

            // Prompt-Forge lightweight toast bus. Server-driven notifications
            // (Flux toasts) can replace this later.
            add(detail) {
                const id = ++this.counter;
                this.toasts.push({
                    id,
                    title: detail.title ?? 'Done',
                    message: detail.message ?? '',
                    type: detail.type ?? 'success',
                });
                setTimeout(() => this.dismiss(id), 4400);
            },

            dismiss(id) {
                this.toasts = this.toasts.filter((toast) => toast.id !== id);
            },
        }));
    });
</script>

<div
    x-data="pfToasts"
    x-on:pf-toast.window="add($event.detail)"
    class="pointer-events-none fixed right-4 bottom-4 z-[70] flex w-[calc(100vw-2rem)] max-w-sm flex-col gap-2"
    role="region"
    aria-label="Notifications"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto flex items-start gap-3 rounded-xl border border-zinc-200 bg-white p-3 shadow-lg shadow-zinc-950/10 dark:border-white/10 dark:bg-zinc-900"
        >
            <template x-if="toast.type === 'success'">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-emerald-500/10 text-emerald-500"><flux:icon.check class="size-3.5" /></span>
            </template>
            <template x-if="toast.type === 'error'">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-red-500/10 text-red-500"><flux:icon.exclamation-triangle class="size-3.5" /></span>
            </template>
            <template x-if="toast.type === 'info'">
                <span class="grid size-6 shrink-0 place-items-center rounded-full bg-brand-500/10 text-brand-500"><flux:icon.information-circle class="size-3.5" /></span>
            </template>

            <div class="min-w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100" x-text="toast.title"></p>
                <p class="mt-0.5 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400" x-show="toast.message" x-text="toast.message"></p>
            </div>

            <button
                type="button"
                class="shrink-0 rounded-md p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:text-zinc-500 dark:hover:bg-white/10 dark:hover:text-zinc-300"
                x-on:click="dismiss(toast.id)"
                aria-label="Dismiss notification"
            >
                <flux:icon.x-mark class="size-3.5" />
            </button>
        </div>
    </template>
</div>