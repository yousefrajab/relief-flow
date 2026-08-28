<x-guest-layout>
    <div class="mb-8">
        <div class="w-11 h-11 rounded-2xl bg-field-500/10 text-field-400 flex items-center justify-center mb-4">
            <x-icon name="lock" class="w-5 h-5" />
        </div>
        <h1 class="text-2xl font-bold text-white">{{ __('Two-Factor Verification') }}</h1>
        <p class="text-sm text-ink-400 mt-1.5">{{ __('Enter the 6-digit code we sent to your email address.') }}</p>
    </div>

    @if ($errors->any())
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-bold rounded-xl px-4 py-3 mb-5">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Verification code')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-2" />
            <input
                id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" autocomplete="one-time-code" required autofocus
                class="block w-full rounded-xl border-ink-200 text-center text-2xl font-bold tracking-[0.5em] shadow-sm focus:border-field-500 focus:ring-field-500 !bg-ink-900/60 !border-white/10 !text-white"
            >
        </div>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-xl bg-gradient-to-r from-field-500 to-field-600 hover:from-field-400 hover:to-field-500 text-white text-sm font-bold shadow-lg shadow-field-950/40 transition-all">
            {{ __('Verify') }} <x-icon name="arrow-right" class="w-4 h-4 rtl:rotate-180" />
        </button>
    </form>

    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-5">
        @csrf
        <button type="submit" class="w-full text-center text-xs font-bold text-field-400 hover:text-field-300">
            {{ __('Resend code') }}
        </button>
    </form>

    <p class="text-center text-xs text-ink-400 mt-6">
        <a href="{{ route('login') }}" class="text-field-400 font-bold hover:text-field-300">{{ __('Back to sign in') }}</a>
    </p>
</x-guest-layout>
