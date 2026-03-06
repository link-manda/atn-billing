<div class="flex items-center justify-between w-full">

    {{-- Kiri: Judul Halaman / Breadcrumbs (Bisa dinamis nanti) --}}
    <div class="flex items-center gap-2">
        <flux:text class="hidden sm:block">
            <span class="font-medium text-zinc-900 dark:text-zinc-100">Welcome back, {{ auth()->user()->name ?? 'Admin' }}!</span>
        </flux:text>
    </div>

    {{-- Kanan: Aksi Cepat & Notifikasi --}}
    <div class="flex items-center gap-3">
        {{-- Search (Mockup visual, nanti bisa di-expand) --}}
        <div class="hidden md:block w-64">
            <flux:input disabled icon="magnifying-glass" placeholder="Search anything (Ctrl+K)..." size="sm" />
        </div>

        {{-- Add New Invoice Shortcut --}}
        <flux:button href="/invoices" variant="primary" size="sm" icon="plus" class="hidden sm:flex">
            New Invoice
        </flux:button>

        {{-- Tombol Notifikasi --}}
        <flux:button variant="subtle" size="sm" icon="bell" badge="3" inset="top bottom" aria-label="Notifications" />
    </div>

</div>
