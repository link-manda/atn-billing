<div class="contents">
    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">

        <flux:sidebar.header>
            <flux:sidebar.brand href="/dashboard" name="ATN Billing">
                <div class="size-6 rounded bg-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </flux:sidebar.brand>
            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="chart-bar" href="/dashboard" :current="request()->routeIs('dashboard')">Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="users" href="/clients" :current="request()->routeIs('clients.*')">Clients</flux:sidebar.item>
            <flux:sidebar.item icon="building-office" href="/clinics" :current="request()->routeIs('clinics.*')">Clinics</flux:sidebar.item>
            <flux:sidebar.item icon="cube" href="/products" :current="request()->routeIs('products.*')">Products</flux:sidebar.item>
            <flux:sidebar.item icon="arrow-path" href="/subscriptions" :current="request()->routeIs('subscriptions.*')">Subscriptions</flux:sidebar.item>
            <flux:sidebar.item icon="document-text" href="/invoices" :current="request()->routeIs('invoices.*')">Invoices</flux:sidebar.item>
            <flux:sidebar.item icon="credit-card" href="/payments" :current="request()->routeIs('payments.*')">Payments</flux:sidebar.item>
            <flux:sidebar.item icon="key" href="/licenses" :current="request()->routeIs('licenses.*')">Licenses</flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <!-- <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="{{ route('profile.edit') }}">Settings</flux:sidebar.item>
        </flux:sidebar.nav> -->

        <!-- Profile Dropdown menggunakan komponen resmi Flux -->
        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:sidebar.profile
                avatar="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'A') }}&color=FFFFFF&background=2563EB"
                name="{{ auth()->user()->name ?? 'Admin' }}"
            />

            <flux:menu>
                <div class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                        {{ auth()->user()->email ?? 'admin@example.com' }}
                    </div>
                </div>

                <flux:menu.item icon="cog-6-tooth" href="{{ route('profile.edit') }}">
                    Settings
                </flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item icon="arrow-right-start-on-rectangle" tag="a" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

    </flux:sidebar>
</div>