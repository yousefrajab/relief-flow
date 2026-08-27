<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReliefFlow</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
                <div class="flex gap-1">
                    <a href="{{ route('locale.switch', 'ar') }}" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'ar' ? 'bg-field-500 text-white' : 'text-ink-500 hover:bg-ink-100' }} transition">AR</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold {{ app()->getLocale() === 'en' ? 'bg-field-500 text-white' : 'text-ink-500 hover:bg-ink-100' }} transition">EN</a>
                </div>
                <a href="{{ route('login') }}" class="text-xs font-bold text-ink-700 hover:text-ink-900">{{ __('Sign in') }}</a>
                <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold">{{ __('Register') }}</a>
            </div>
        </div>
    </header>

    <section class="max-w-4xl mx-auto px-6 py-20 text-center space-y-6">
        <span class="inline-block text-[11px] font-bold text-field-700 bg-field-50 border border-field-200 rounded-full px-3 py-1.5">{{ __('Humanitarian Logistics Coordination Platform') }}</span>
        <h1 class="text-3xl md:text-5xl font-extrabold text-ink-900 leading-tight">{{ __('Relief supplies, tracked from warehouse to family.') }}</h1>
        <p class="text-sm md:text-base text-ink-500 max-w-2xl mx-auto leading-relaxed">
            {{ __('ReliefFlow connects depot managers and field coordinators so relief items move where they are needed, with verified delivery and full visibility at every step.') }}
        </p>
        <div class="flex items-center justify-center gap-3 pt-2">
            <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl bg-field-600 hover:bg-field-700 text-white text-sm font-bold shadow-lg shadow-field-100">{{ __('Get started') }}</a>
            <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-white border border-ink-200 hover:border-ink-300 text-ink-700 text-sm font-bold">{{ __('Sign in') }}</a>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-6 pb-16">
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white border border-ink-100 rounded-2xl p-6 text-center">
                <p class="text-2xl font-extrabold text-field-600">{{ number_format($stats['delivered']) }}</p>
                <p class="text-[11px] font-bold text-ink-400 mt-1">{{ __('Deliveries completed') }}</p>
            </div>
            <div class="bg-white border border-ink-100 rounded-2xl p-6 text-center">
                <p class="text-2xl font-extrabold text-field-600">{{ number_format($stats['warehouses']) }}</p>
                <p class="text-[11px] font-bold text-ink-400 mt-1">{{ __('Active warehouses') }}</p>
            </div>
            <div class="bg-white border border-ink-100 rounded-2xl p-6 text-center">
                <p class="text-2xl font-extrabold text-field-600">{{ number_format($stats['coordinators']) }}</p>
                <p class="text-[11px] font-bold text-ink-400 mt-1">{{ __('Field coordinators') }}</p>
            </div>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-6 pb-24">
        <h2 class="text-center text-xl font-bold text-ink-900 mb-10">{{ __('How it works') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center space-y-2">
                <div class="w-10 h-10 rounded-xl bg-field-50 text-field-600 font-extrabold flex items-center justify-center mx-auto">1</div>
                <p class="text-xs font-bold text-ink-900">{{ __('Request') }}</p>
                <p class="text-[11px] text-ink-500 leading-relaxed">{{ __('A field coordinator submits an aid request with the items and quantities needed.') }}</p>
            </div>
            <div class="text-center space-y-2">
                <div class="w-10 h-10 rounded-xl bg-field-50 text-field-600 font-extrabold flex items-center justify-center mx-auto">2</div>
                <p class="text-xs font-bold text-ink-900">{{ __('Dispatch') }}</p>
                <p class="text-[11px] text-ink-500 leading-relaxed">{{ __('A depot manager picks the best warehouse and dispatches a driver with a QR-coded manifest.') }}</p>
            </div>
            <div class="text-center space-y-2">
                <div class="w-10 h-10 rounded-xl bg-field-50 text-field-600 font-extrabold flex items-center justify-center mx-auto">3</div>
                <p class="text-xs font-bold text-ink-900">{{ __('Track') }}</p>
                <p class="text-[11px] text-ink-500 leading-relaxed">{{ __('Anyone can scan the manifest QR code to see the live status of the shipment.') }}</p>
            </div>
            <div class="text-center space-y-2">
                <div class="w-10 h-10 rounded-xl bg-field-50 text-field-600 font-extrabold flex items-center justify-center mx-auto">4</div>
                <p class="text-xs font-bold text-ink-900">{{ __('Confirm') }}</p>
                <p class="text-[11px] text-ink-500 leading-relaxed">{{ __('The coordinator confirms receipt in the field, closing the loop with a verified record.') }}</p>
            </div>
        </div>
    </section>

    <footer class="border-t border-ink-100 py-8 text-center">
        <p class="text-[11px] text-ink-400">ReliefFlow</p>
    </footer>
</body>
</html>
