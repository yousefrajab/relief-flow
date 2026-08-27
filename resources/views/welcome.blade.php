<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReliefFlow</title>
    @include('partials.theme-init')

    @vite(['resources/css/app.css'])
</head>
<body class="bg-ink-50 text-ink-900">
    <header class="border-b border-ink-100">
        <div class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-field-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-ink-900">ReliefFlow</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex gap-1 items-center">
                    <a href="{{ route('locale.switch', 'ar') }}" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'ar' ? 'bg-field-500 text-white' : 'text-ink-500 hover:bg-ink-100' }} transition">AR</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'en' ? 'bg-field-500 text-white' : 'text-ink-500 hover:bg-ink-100' }} transition">EN</a>
                    <x-theme-toggle class="w-8 h-8 flex items-center justify-center rounded-lg text-ink-500 hover:bg-ink-100 transition shrink-0" />
                </div>
                <a href="{{ route('login') }}" class="text-xs font-bold text-ink-700 hover:text-ink-900">{{ __('Sign in') }}</a>
                <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold">{{ __('Register') }}</a>
            </div>
        </div>
    </header>

    <section class="max-w-6xl mx-auto px-6 py-16 md:py-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-field-700 bg-field-50 border border-field-200 rounded-full px-3 py-1.5">
                <x-icon name="sparkles" class="w-3.5 h-3.5" /> {{ __('Humanitarian Logistics Coordination Platform') }}
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-ink-900 leading-tight">{{ __('Relief supplies, tracked from warehouse to family.') }}</h1>
            <p class="text-sm md:text-base text-ink-500 max-w-xl leading-relaxed">
                {{ __('ReliefFlow connects depot managers and field coordinators so relief items move where they are needed, with AI-assisted triage, verified delivery, and full visibility at every step.') }}
            </p>
            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-6 py-3 rounded-xl bg-field-600 hover:bg-field-700 text-white text-sm font-bold shadow-lg shadow-field-100">
                    {{ __('Get started') }} <x-icon name="arrow-right" class="w-4 h-4 rtl:rotate-180" />
                </a>
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-white border border-ink-200 hover:border-ink-300 text-ink-700 text-sm font-bold">{{ __('Sign in') }}</a>
            </div>

            <div class="grid grid-cols-3 gap-4 pt-6">
                <div class="bg-white border border-ink-100 rounded-2xl p-4">
                    <x-icon name="check-circle" class="w-5 h-5 text-field-500 mb-2" />
                    <p class="text-xl font-extrabold text-ink-900">{{ number_format($stats['delivered']) }}</p>
                    <p class="text-[10px] font-bold text-ink-400 mt-0.5">{{ __('Deliveries completed') }}</p>
                </div>
                <div class="bg-white border border-ink-100 rounded-2xl p-4">
                    <x-icon name="warehouse" class="w-5 h-5 text-field-500 mb-2" />
                    <p class="text-xl font-extrabold text-ink-900">{{ number_format($stats['warehouses']) }}</p>
                    <p class="text-[10px] font-bold text-ink-400 mt-0.5">{{ __('Active warehouses') }}</p>
                </div>
                <div class="bg-white border border-ink-100 rounded-2xl p-4">
                    <x-icon name="users" class="w-5 h-5 text-field-500 mb-2" />
                    <p class="text-xl font-extrabold text-ink-900">{{ number_format($stats['coordinators']) }}</p>
                    <p class="text-[10px] font-bold text-ink-400 mt-0.5">{{ __('Field coordinators') }}</p>
                </div>
            </div>
        </div>

        <div class="relative">
            <x-hero-illustration class="w-full h-auto drop-shadow-xl" />
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-6 pb-24">
        <h2 class="text-center text-xl font-bold text-ink-900 mb-10">{{ __('How it works') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center space-y-2">
                <div class="relative w-12 h-12 rounded-2xl bg-field-50 text-field-600 flex items-center justify-center mx-auto">
                    <x-icon name="clipboard" class="w-6 h-6" />
                    <span class="absolute -top-1.5 -end-1.5 w-5 h-5 rounded-full bg-field-600 text-white text-[10px] font-extrabold flex items-center justify-center">1</span>
                </div>
                <p class="text-xs font-bold text-ink-900">{{ __('Request') }}</p>
                <p class="text-[11px] text-ink-500 leading-relaxed">{{ __('A field coordinator submits an aid request with the items and quantities needed.') }}</p>
            </div>
            <div class="text-center space-y-2">
                <div class="relative w-12 h-12 rounded-2xl bg-field-50 text-field-600 flex items-center justify-center mx-auto">
                    <x-icon name="route" class="w-6 h-6" />
                    <span class="absolute -top-1.5 -end-1.5 w-5 h-5 rounded-full bg-field-600 text-white text-[10px] font-extrabold flex items-center justify-center">2</span>
                </div>
                <p class="text-xs font-bold text-ink-900">{{ __('Dispatch') }}</p>
                <p class="text-[11px] text-ink-500 leading-relaxed">{{ __('A depot manager picks the best warehouse and dispatches a driver with a QR-coded manifest.') }}</p>
            </div>
            <div class="text-center space-y-2">
                <div class="relative w-12 h-12 rounded-2xl bg-field-50 text-field-600 flex items-center justify-center mx-auto">
                    <x-icon name="qr" class="w-6 h-6" />
                    <span class="absolute -top-1.5 -end-1.5 w-5 h-5 rounded-full bg-field-600 text-white text-[10px] font-extrabold flex items-center justify-center">3</span>
                </div>
                <p class="text-xs font-bold text-ink-900">{{ __('Track') }}</p>
                <p class="text-[11px] text-ink-500 leading-relaxed">{{ __('Anyone can scan the manifest QR code to see the live status of the shipment.') }}</p>
            </div>
            <div class="text-center space-y-2">
                <div class="relative w-12 h-12 rounded-2xl bg-field-50 text-field-600 flex items-center justify-center mx-auto">
                    <x-icon name="shield-check" class="w-6 h-6" />
                    <span class="absolute -top-1.5 -end-1.5 w-5 h-5 rounded-full bg-field-600 text-white text-[10px] font-extrabold flex items-center justify-center">4</span>
                </div>
                <p class="text-xs font-bold text-ink-900">{{ __('Confirm') }}</p>
                <p class="text-[11px] text-ink-500 leading-relaxed">{{ __('The coordinator confirms receipt in the field, closing the loop with a verified record.') }}</p>
            </div>
        </div>
    </section>

    <section class="bg-ink-900">
        <div class="max-w-5xl mx-auto px-6 py-20">
            <div class="text-center mb-12">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-field-300 bg-white/5 border border-white/10 rounded-full px-3 py-1.5">
                    <x-icon name="sparkles" class="w-3.5 h-3.5" /> {{ __('AI-assisted') }}
                </span>
                <h2 class="text-xl md:text-2xl font-bold text-white mt-4">{{ __('Smart features, safe by default') }}</h2>
                <p class="text-xs text-ink-400 mt-2 max-w-xl mx-auto leading-relaxed">{{ __('Every AI feature runs in a safe simulation mode with zero external calls until a real key is connected — nothing about the platform depends on it.') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                    <x-icon name="sparkles" class="w-5 h-5 text-field-300 mb-3" />
                    <p class="text-xs font-bold text-white">{{ __('Priority triage') }}</p>
                    <p class="text-[11px] text-ink-400 mt-1.5 leading-relaxed">{{ __('Every request is classified normal, high, or critical from its notes.') }}</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                    <x-icon name="route" class="w-5 h-5 text-field-300 mb-3" />
                    <p class="text-xs font-bold text-white">{{ __('Smart warehouse matching') }}</p>
                    <p class="text-[11px] text-ink-400 mt-1.5 leading-relaxed">{{ __('Warehouses ranked by real distance and stock coverage for one-click dispatch.') }}</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                    <x-icon name="camera" class="w-5 h-5 text-field-300 mb-3" />
                    <p class="text-xs font-bold text-white">{{ __('Delivery photo verification') }}</p>
                    <p class="text-[11px] text-ink-400 mt-1.5 leading-relaxed">{{ __('An optional delivery photo is checked against the manifest for a plausibility flag.') }}</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                    <x-icon name="chart" class="w-5 h-5 text-field-300 mb-3" />
                    <p class="text-xs font-bold text-white">{{ __('Humanitarian impact report') }}</p>
                    <p class="text-[11px] text-ink-400 mt-1.5 leading-relaxed">{{ __('A written narrative summary built strictly from real platform statistics.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-6 py-20">
        <div class="bg-gradient-to-br from-field-600 to-field-800 rounded-3xl px-8 py-14 text-center relative overflow-hidden">
            <div class="absolute -top-10 -end-10 w-56 h-56 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-16 -start-10 w-56 h-56 rounded-full bg-white/5"></div>
            <div class="relative">
                <h2 class="text-xl md:text-2xl font-bold text-white">{{ __('Ready to coordinate relief with confidence?') }}</h2>
                <p class="text-xs text-field-100 mt-3 max-w-md mx-auto leading-relaxed">{{ __('Register as a depot manager or field coordinator — an administrator will review and approve your account.') }}</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 mt-6 px-6 py-3 rounded-xl bg-white text-field-700 text-sm font-bold hover:bg-field-50 transition">
                    {{ __('Create your account') }} <x-icon name="arrow-right" class="w-4 h-4 rtl:rotate-180" />
                </a>
            </div>
        </div>
    </section>

    <footer class="border-t border-ink-100 py-8 text-center">
        <p class="text-[11px] text-ink-400">ReliefFlow — {{ __('Humanitarian Logistics Coordination Platform') }}</p>
    </footer>
</body>
</html>
