<x-layouts.guest>
    <x-shared.auth-card
        eyebrow="Email verification"
        title="Verify your email address"
        description="Almost done. Click the verification link we sent to your inbox to activate your account."
    >
        @if (session('status') == 'verification-link-sent')
            <div class="flex items-start gap-2.5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-3.5 py-2.5 text-sm text-emerald-700 dark:text-emerald-300">
                <flux:icon.check-circle class="mt-0.5 size-4 shrink-0" />
                <span>{{ __('A new verification link has been sent to your email address.') }}</span>
            </div>
        @endif

        <div class="space-y-5">
            <p class="text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                If you didn't receive the email, resend the link below or use the logout form to switch accounts.
            </p>

            <div class="flex flex-col gap-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <flux:button variant="primary" type="submit" class="w-full justify-center">Resend verification email</flux:button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:button variant="subtle" type="submit" class="w-full justify-center">Log out</flux:button>
                </form>
            </div>
        </div>
    </x-shared.auth-card>
</x-layouts.guest>