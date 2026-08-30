<section class="rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-white/10 dark:bg-zinc-900/60">
    <header class="border-b border-zinc-100 px-5 py-4 dark:border-white/5">
        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Profile</h2>
        <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">How you appear across the workspace.</p>
    </header>
    <div class="space-y-5 p-5 sm:p-6">
        <div class="flex items-center gap-4">
            <x-avatar name="Darshan Patel" class="size-14 !text-lg" />
            <div>
                <flux:button variant="subtle" size="sm" x-data x-on:click="$dispatch('pf-toast', { title: 'Upload simulated', message: 'Avatar upload is mocked (no backend).' })">Change photo</flux:button>
                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">JPG or PNG, max 2MB.</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <flux:field>
                <flux:label>First name</flux:label>
                <flux:input value="Darshan" />
            </flux:field>
            <flux:field>
                <flux:label>Last name</flux:label>
                <flux:input value="Patel" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input type="email" value="darshan@prompt-forge.app" />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Bio</flux:label>
            <flux:textarea rows="3" placeholder="A short bio shown to your team…" />
        </flux:field>

        <flux:field>
            <flux:label>Timezone</flux:label>
            <flux:select>
                <flux:select.option value="UTC">UTC</flux:select.option>
                <flux:select.option value="IST">India Standard Time (UTC+5:30)</flux:select.option>
                <flux:select.option value="PST">Pacific Standard Time</flux:select.option>
                <flux:select.option value="EST">Eastern Standard Time</flux:select.option>
            </flux:select>
        </flux:field>

        <div class="flex items-center justify-end border-t border-zinc-100 pt-5 dark:border-white/5">
            <flux:button variant="primary" icon="check" x-data x-on:click="$dispatch('pf-toast', { icon: 'check-circle', title: 'Profile saved', message: 'Changes are stored in mock state only.' })">Save changes</flux:button>
        </div>
    </div>
</section>