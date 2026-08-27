<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">{{ __('Reset your password') }}</h1>
        <p class="text-sm text-ink-400 mt-1.5">{{ __("Enter your email address and we'll send you a link to reset your password.") }}</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-2" />
            <div class="relative">
                <x-icon name="mail" class="w-4.5 h-4.5 text-ink-500 absolute top-1/2 -translate-y-1/2 start-4 pointer-events-none" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="you@company.com" class="!bg-ink-900/60 !border-white/10 !text-white placeholder:!text-ink-500 !ps-11" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-xl bg-gradient-to-r from-field-500 to-field-600 hover:from-field-400 hover:to-field-500 text-white text-sm font-bold shadow-lg shadow-field-950/40 transition-all">
            {{ __('Send password reset link') }} <x-icon name="arrow-right" class="w-4 h-4 rtl:rotate-180" />
        </button>
    </form>

    <p class="text-center text-xs text-ink-400 mt-6">
        <a href="{{ route('login') }}" class="text-field-400 font-bold hover:text-field-300">{{ __('Back to sign in') }}</a>
    </p>
</x-guest-layout>
