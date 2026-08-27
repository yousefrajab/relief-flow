<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReliefFlow</title>
    @include('partials.theme-init')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-ink-900 flex">
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-field-600 to-field-900 p-12 flex-col justify-between">
        <div class="absolute -top-24 -end-24 w-72 h-72 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-32 -start-16 w-80 h-80 rounded-full bg-white/5"></div>

        <a href="{{ url('/') }}" class="relative flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span class="text-xl font-bold text-white">ReliefFlow</span>
        </a>

        <div class="relative">
            <x-hero-illustration class="w-full max-w-md drop-shadow-xl" />
            <h2 class="text-xl font-bold text-white mt-8 leading-snug">{{ __('Relief supplies, tracked from warehouse to family.') }}</h2>
            <p class="text-xs text-field-100 mt-2 max-w-sm leading-relaxed">{{ __('Humanitarian Logistics Coordination Platform') }}</p>
        </div>

        <p class="relative text-[11px] text-field-200">ReliefFlow &copy; {{ date('Y') }}</p>
    </div>

    <div class="flex-1 flex flex-col min-h-screen">
        <div class="flex items-center justify-between p-6">
            <a href="{{ url('/') }}" class="lg:hidden flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-field-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-ink-900">ReliefFlow</span>
            </a>
            <div class="flex-1 lg:flex-none"></div>
            <div class="flex items-center gap-2">
                <a href="{{ route('locale.switch', 'ar') }}" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'ar' ? 'bg-field-500 text-white' : 'text-ink-500 hover:bg-ink-100' }} transition">AR</a>
                <a href="{{ route('locale.switch', 'en') }}" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'en' ? 'bg-field-500 text-white' : 'text-ink-500 hover:bg-ink-100' }} transition">EN</a>
                <x-theme-toggle class="w-8 h-8 flex items-center justify-center rounded-lg text-ink-500 hover:bg-ink-100 transition shrink-0" />
            </div>
        </div>

        <div class="flex-grow flex items-center justify-center p-6">
            <div class="w-full max-w-sm">
                @if(session('success'))
                    <div class="bg-field-50 border border-field-200 text-field-800 text-xs font-bold rounded-2xl px-4 py-3 mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
