<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-sm space-y-8">

            {{-- Logo & Brand --}}
            <div class="text-center">
                <div class="mx-auto w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">ATN Billing</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Billing & Invoice Management Platform</p>
            </div>

            {{-- Session Status (Forgot password success, etc.) --}}
            @if (session('status'))
                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3">
                    <p class="text-sm text-green-700 dark:text-green-400">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Login Card --}}
            <flux:card class="space-y-6 shadow-xl">
                <div>
                    <flux:heading size="lg">Sign in to your account</flux:heading>
                    <flux:text class="mt-1">Welcome back! Please enter your credentials.</flux:text>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <flux:field>
                        <flux:label>Email address</flux:label>
                        <flux:input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="admin@example.com"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        @error('email')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    {{-- Password --}}
                    <flux:field>
                        <div class="flex items-center justify-between mb-1">
                            <flux:label>Password</flux:label>
                            @if (Route::has('password.request'))
                                <flux:link href="{{ route('password.request') }}" variant="subtle" class="text-sm">
                                    Forgot password?
                                </flux:link>
                            @endif
                        </div>
                        <flux:input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            viewable
                        />
                        @error('password')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    {{-- Remember Me --}}
                    <flux:field variant="inline">
                        <flux:checkbox name="remember" id="remember_me" />
                        <flux:label for="remember_me">Remember me</flux:label>
                    </flux:field>

                    {{-- Submit --}}
                    <flux:button type="submit" variant="primary" class="w-full">
                        Sign in
                    </flux:button>
                </form>
            </flux:card>

        </div>
    </div>
</x-guest-layout>
