<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReliefFlow — {{ $title ?? __('Dashboard') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-ink-50 text-ink-900">
    <div class="min-h-screen flex flex-col md:flex-row">
        <aside class="w-full md:w-64 shrink-0 bg-ink-900 text-ink-200 flex flex-col justify-between p-5">
            <div>
                <div class="flex items-center gap-2.5 px-2 py-3 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-field-500 flex items-center justify-center shadow-lg shadow-field-950/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">ReliefFlow</span>
                </div>

                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl bg-white/5 text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        {{ __('Dashboard') }}
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <a href="#warehouses" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl text-ink-300 hover:bg-white/5 hover:text-white transition-colors">{{ __('Warehouses') }}</a>
                        <a href="#items" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl text-ink-300 hover:bg-white/5 hover:text-white transition-colors">{{ __('Relief Items') }}</a>
                        <a href="#aid-requests" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl text-ink-300 hover:bg-white/5 hover:text-white transition-colors">{{ __('Aid Requests') }}</a>
                        <a href="#accounts" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl text-ink-300 hover:bg-white/5 hover:text-white transition-colors">{{ __('Accounts') }}</a>
                    @elseif(auth()->user()->role === 'depot_manager')
                        <a href="#inventory" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl text-ink-300 hover:bg-white/5 hover:text-white transition-colors">{{ __('Inventory') }}</a>
                        <a href="#pending-requests" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl text-ink-300 hover:bg-white/5 hover:text-white transition-colors">{{ __('Pending Requests') }}</a>
                        <a href="#shipments" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl text-ink-300 hover:bg-white/5 hover:text-white transition-colors">{{ __('Shipments') }}</a>
                    @else
                        <a href="#new-request" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl text-ink-300 hover:bg-white/5 hover:text-white transition-colors">{{ __('New Aid Request') }}</a>
                        <a href="#my-requests" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl text-ink-300 hover:bg-white/5 hover:text-white transition-colors">{{ __('My Requests') }}</a>
                    @endif
                </nav>
            </div>

            <div class="space-y-3">
                <div class="flex gap-2 px-1">
                    <a href="{{ route('locale.switch', 'ar') }}" class="flex-1 text-center px-2 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'ar' ? 'bg-field-500 text-white' : 'bg-white/5 text-ink-300 hover:bg-white/10' }} transition">AR</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="flex-1 text-center px-2 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'en' ? 'bg-field-500 text-white' : 'bg-white/5 text-ink-300 hover:bg-white/10' }} transition">EN</a>
                </div>

                <div class="bg-white/5 rounded-2xl p-3.5">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-ink-400 mt-0.5">
                        {{ match(auth()->user()->role) {
                            'admin' => __('Administrator'),
                            'depot_manager' => __('Depot Manager'),
                            default => __('Field Coordinator'),
                        } }}
                    </p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2.5">
                        @csrf
                        <button type="submit" class="w-full text-center px-2 py-1.5 rounded-lg text-[11px] font-bold bg-white/5 hover:bg-rose-500/20 text-ink-300 hover:text-rose-300 transition">
                            {{ __('Log out') }}
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-grow min-h-screen">
            <div class="p-5 md:p-8 max-w-6xl mx-auto space-y-6">
                @if(session('success'))
                    <div class="bg-field-50 border border-field-200 text-field-800 text-xs font-bold rounded-2xl px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold rounded-2xl px-4 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold rounded-2xl px-4 py-3 space-y-1">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
