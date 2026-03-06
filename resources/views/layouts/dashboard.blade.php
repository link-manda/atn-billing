<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ATN Billing Platform') }}</title>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Vite: CSS + JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    <div class="flex min-h-screen">
    {{-- Sidebar Livewire Component --}}
        @livewire('layout.sidebar')

        {{-- Main Area --}}
        <div class="flex flex-col flex-1 min-w-0">

            {{-- Mobile Header (hamburger + user) --}}
            <flux:header class="lg:hidden border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-4">
                <flux:sidebar.toggle icon="bars-2" inset="left" />
                <flux:spacer />
                <div class="flex items-center gap-2 text-sm font-medium text-zinc-700 dark:text-zinc-200">
                    {{ auth()->user()->name ?? 'Admin' }}
                </div>
            </flux:header>

            {{-- Topbar (desktop) --}}
            <header class="hidden lg:flex border-b border-zinc-200 dark:border-zinc-700 bg-white/80 dark:bg-zinc-800/80 backdrop-blur-md px-6 py-3 items-center shrink-0 sticky top-0 z-10 w-full">
                @livewire('layout.topbar')
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>


    @livewireScripts
    @fluxScripts

</body>
</html>