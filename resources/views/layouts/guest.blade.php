<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReliefFlow</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1f9a79">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen bg-ink-950 text-white flex relative overflow-hidden">
    <div class="pointer-events-none absolute -bottom-48 -start-48 w-[620px] h-[620px] rounded-full bg-field-600/25 blur-3xl"></div>
    <div class="pointer-events-none absolute -top-32 end-0 w-[420px] h-[420px] rounded-full bg-field-500/10 blur-3xl"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 22px 22px;"></div>

    <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12 border-e border-white/5">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-field-500 flex items-center justify-center shadow-lg shadow-field-950/40 shrink-0">
                <svg class="w-5.5 h-5.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-white leading-tight">ReliefFlow</p>
                <p class="text-[11px] text-ink-400">{{ __('Humanitarian Logistics Coordination Platform') }}</p>
            </div>
        </a>

        <div class="max-w-md">
            <h1 class="text-3xl font-bold text-white leading-tight">{{ __('Relief supplies, tracked from warehouse to family.') }}</h1>
            <p class="text-sm text-ink-400 mt-3 leading-relaxed">{{ __('ReliefFlow connects depot managers and field coordinators so relief items move where they are needed, with AI-assisted triage, verified delivery, and full visibility at every step.') }}</p>

            <div class="mt-9 space-y-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-field-500/15 border border-field-500/20 text-field-400 flex items-center justify-center shrink-0"><x-icon name="qr" class="w-4.5 h-4.5" /></div>
                    <p class="text-sm text-ink-200 font-semibold">{{ __('Live tracking with QR-verified delivery') }}</p>
                </div>
                <div class="flex items-center gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-field-500/15 border border-field-500/20 text-field-400 flex items-center justify-center shrink-0"><x-icon name="shield-check" class="w-4.5 h-4.5" /></div>
                    <p class="text-sm text-ink-200 font-semibold">{{ __('Role-based access for admins, depots, and coordinators') }}</p>
                </div>
                <div class="flex items-center gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-field-500/15 border border-field-500/20 text-field-400 flex items-center justify-center shrink-0"><x-icon name="users" class="w-4.5 h-4.5" /></div>
                    <p class="text-sm text-ink-200 font-semibold">{{ __('Built for teams operating in the field') }}</p>
                </div>
            </div>
        </div>

        <p class="text-[11px] text-ink-500">ReliefFlow &copy; {{ date('Y') }} &mdash; {{ __('All rights reserved') }}</p>
    </div>

    <div class="flex-1 flex flex-col min-h-screen relative">
        <div class="flex items-center justify-between p-6">
            <a href="{{ url('/') }}" class="lg:hidden flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-field-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-white">ReliefFlow</span>
            </a>
            <div class="flex-1 lg:flex-none"></div>
            <div class="flex items-center gap-2">
                <a href="{{ route('locale.switch', 'ar') }}" class="px-3 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'ar' ? 'bg-field-500 text-white' : 'bg-white/5 text-ink-300 hover:bg-white/10' }} transition">العربية</a>
                <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'en' ? 'bg-field-500 text-white' : 'bg-white/5 text-ink-300 hover:bg-white/10' }} transition">English</a>
            </div>
        </div>

        <div class="flex-grow flex items-center justify-center p-6">
            <div class="w-full max-w-lg">
                @if(session('success'))
                    <div class="bg-field-500/10 border border-field-500/20 text-field-300 text-xs font-bold rounded-2xl px-4 py-3 mb-5">
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot }}

                <p class="flex items-center justify-center gap-1.5 text-[11px] text-ink-500 mt-8">
                    <x-icon name="lock" class="w-3.5 h-3.5" /> {{ __('Secured area — authorized personnel only') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
