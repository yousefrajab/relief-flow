<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReliefFlow — {{ $title ?? __('Dashboard') }}</title>
    @include('partials.theme-init')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-ink-50 text-ink-900">
    <div class="min-h-screen flex flex-col md:flex-row">
        <aside class="w-full md:w-64 shrink-0 bg-ink-900 text-ink-200 flex flex-col justify-between p-5 md:sticky md:top-0 md:h-screen md:overflow-y-auto">
            <div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-2 py-3 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-field-500 flex items-center justify-center shadow-lg shadow-field-950/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">ReliefFlow</span>
                </a>

                @php $current = request()->route()->getName(); @endphp
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ $current === 'dashboard' ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}">
                        <x-icon name="dashboard" class="w-4.5 h-4.5 shrink-0" /> {{ __('Dashboard') }}
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('warehouses.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ str_starts_with($current, 'warehouses.') ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}"><x-icon name="warehouse" class="w-4.5 h-4.5 shrink-0" /> {{ __('Warehouses') }}</a>
                        <a href="{{ route('items.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ str_starts_with($current, 'items.') ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}"><x-icon name="box" class="w-4.5 h-4.5 shrink-0" /> {{ __('Relief Items') }}</a>
                    @endif

                    @if(in_array(auth()->user()->role, ['admin', 'depot_manager']))
                        <a href="{{ route('inventory.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ str_starts_with($current, 'inventory.') ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}"><x-icon name="inventory" class="w-4.5 h-4.5 shrink-0" /> {{ __('Inventory') }}</a>
                    @endif

                    <a href="{{ route('aid-requests.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ str_starts_with($current, 'aid-requests.') ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}"><x-icon name="clipboard" class="w-4.5 h-4.5 shrink-0" /> {{ __('Aid Requests') }}</a>

                    <a href="{{ route('map.show') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ $current === 'map.show' ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}"><x-icon name="map" class="w-4.5 h-4.5 shrink-0" /> {{ __('Map') }}</a>

                    @if(in_array(auth()->user()->role, ['admin', 'depot_manager']))
                        <a href="{{ route('reports.show') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ $current === 'reports.show' ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}"><x-icon name="chart" class="w-4.5 h-4.5 shrink-0" /> {{ __('Impact Report') }}</a>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.users') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ $current === 'admin.users' ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}"><x-icon name="users" class="w-4.5 h-4.5 shrink-0" /> {{ __('Accounts') }}</a>
                    @endif

                    <div class="pt-2 mt-2 border-t border-white/5 space-y-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ str_starts_with($current, 'profile.') ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}"><x-icon name="user" class="w-4.5 h-4.5 shrink-0" /> {{ __('Profile') }}</a>
                        <a href="{{ route('help') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs font-bold rounded-xl transition-colors {{ $current === 'help' ? 'bg-field-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}"><x-icon name="help" class="w-4.5 h-4.5 shrink-0" /> {{ __('Help') }}</a>
                    </div>
                </nav>
            </div>

            <div class="space-y-3">
                <div class="flex gap-2 px-1">
                    <a href="{{ route('locale.switch', 'ar') }}" class="flex-1 text-center px-2 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'ar' ? 'bg-field-500 text-white' : 'bg-white/5 text-ink-300 hover:bg-white/10' }} transition">AR</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="flex-1 text-center px-2 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'en' ? 'bg-field-500 text-white' : 'bg-white/5 text-ink-300 hover:bg-white/10' }} transition">EN</a>
                    <x-theme-toggle class="w-9 h-9 flex items-center justify-center rounded-lg bg-white/5 text-ink-300 hover:bg-white/10 transition shrink-0" />
                </div>

                <div class="bg-white/5 rounded-2xl p-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-field-500/20 border border-field-400/30 flex items-center justify-center text-field-300 font-bold text-xs shrink-0 overflow-hidden">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-ink-400 mt-0.5">
                                {{ match(auth()->user()->role) {
                                    'admin' => __('Administrator'),
                                    'depot_manager' => __('Depot Manager'),
                                    default => __('Field Coordinator'),
                                } }}
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2.5">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-lg text-[11px] font-bold bg-white/5 hover:bg-rose-500/20 text-ink-300 hover:text-rose-300 transition">
                            <x-icon name="logout" class="w-3.5 h-3.5" /> {{ __('Log out') }}
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-grow min-h-screen app-main-bg">
            <div class="flex justify-end px-5 md:px-8 pt-5 md:pt-8 max-w-7xl mx-auto">
                @php $unread = auth()->user()->unreadNotifications; @endphp
                <div class="relative" x-data="{ open: false }">
                    <button type="button" x-on:click="open = !open" x-on:click.outside="open = false" class="relative w-10 h-10 rounded-xl bg-white border border-ink-100 flex items-center justify-center hover:border-field-300 transition-colors">
                        <x-icon name="bell" class="w-4.5 h-4.5 text-ink-600" />
                        @if($unread->count() > 0)
                            <span class="absolute -top-1 -{{ app()->getLocale() === 'ar' ? 'start' : 'end' }}-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center">{{ $unread->count() }}</span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak x-transition class="absolute end-0 mt-2 w-80 max-w-[90vw] bg-white border border-ink-100 rounded-2xl shadow-xl z-50 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-ink-100">
                            <p class="text-xs font-bold text-ink-900">{{ __('Notifications') }}</p>
                            @if($unread->count() > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-bold text-field-600 hover:text-field-700">{{ __('Mark all read') }}</button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-ink-50">
                            @forelse($unread->take(8) as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 hover:bg-ink-50 transition-colors">
                                    <p class="text-[11px] text-ink-700 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                    <p class="text-[10px] text-ink-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </a>
                            @empty
                                <p class="px-4 py-6 text-[11px] text-ink-400 text-center">{{ __('No new notifications.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5 md:p-8 max-w-7xl mx-auto space-y-6">
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
