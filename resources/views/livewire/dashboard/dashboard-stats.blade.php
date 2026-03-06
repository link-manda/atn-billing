<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Dashboard</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Welcome back. Here's your business overview.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

        {{-- Total Clients --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Clients</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <flux:icon.users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div>
                <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $total_clients }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Registered clients</p>
            </div>
        </div>

        {{-- Total Clinics --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Clinics</span>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <flux:icon.building-office class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
            <div>
                <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $total_clinics }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Active clinic locations</p>
            </div>
        </div>

        {{-- Active Subscriptions --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Active Subscriptions</span>
                <div class="w-9 h-9 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                    <flux:icon.arrow-path class="w-5 h-5 text-violet-600 dark:text-violet-400" />
                </div>
            </div>
            <div>
                <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $active_subscriptions }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Running subscriptions</p>
            </div>
        </div>

        {{-- Unpaid Invoices --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Unpaid Invoices</span>
                <div class="w-9 h-9 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <flux:icon.document-text class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
            <div>
                <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $unpaid_invoices }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Awaiting payment</p>
            </div>
        </div>

        {{-- Monthly Revenue --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Monthly Revenue</span>
                <div class="w-9 h-9 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                    <flux:icon.currency-dollar class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div>
                <p class="text-3xl font-bold text-zinc-900 dark:text-white">Rp {{ number_format($monthly_revenue, 0, ',', '.') }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ now()->format('F Y') }}</p>
            </div>
        </div>

    </div>

</div>
