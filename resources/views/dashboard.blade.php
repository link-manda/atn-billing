<x-dashboard-layout>
    <div class="space-y-6">
        {{-- KPI Cards (Row 1) --}}
        @livewire('dashboard.dashboard-stats')

        {{-- Main Charts (Row 2) --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Revenue Trend (Takes 2/3 width on large screens) --}}
            <div class="xl:col-span-2">
                @livewire('dashboard.revenue-chart')
            </div>

            {{-- Invoice Status (Takes 1/3 width on large screens) --}}
            <div class="xl:col-span-1">
                @livewire('dashboard.invoice-status-chart')
            </div>
        </div>

        {{-- Recent Activity (Row 3) --}}
        <div>
            @livewire('dashboard.recent-invoices')
        </div>
    </div>
</x-dashboard-layout>