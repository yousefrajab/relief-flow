<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReliefFlow</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink-900 flex items-center justify-center p-4">
    <div class="absolute top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} flex gap-2">
        <a href="{{ route('locale.switch', 'ar') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ app()->getLocale() === 'ar' ? 'bg-field-500 text-white' : 'bg-white/10 text-ink-200 hover:bg-white/20' }} transition">العربية</a>
        <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ app()->getLocale() === 'en' ? 'bg-field-500 text-white' : 'bg-white/10 text-ink-200 hover:bg-white/20' }} transition">English</a>
    </div>

    <div class="w-full max-w-md">
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="w-11 h-11 rounded-2xl bg-field-500 flex items-center justify-center shadow-lg shadow-field-950/40">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span class="text-2xl font-bold text-white tracking-tight">ReliefFlow</span>
        </div>

        <div class="bg-ink-800/60 border border-white/5 rounded-3xl shadow-2xl shadow-black/40 p-8 backdrop-blur">
            {{ $slot }}
        </div>

        <p class="text-center text-xs text-ink-400 mt-6">{{ __('Humanitarian Logistics Coordination Platform') }}</p>
    </div>
</body>
</html>
