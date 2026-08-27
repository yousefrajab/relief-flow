<x-guest-layout>
    <h2 class="text-lg font-bold text-white text-center mb-1">{{ __('Reset your password') }}</h2>
    <p class="text-xs text-ink-400 text-center mb-6">{{ __("Enter your email address and we'll send you a link to reset your password.") }}</p>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" class="!text-ink-300" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus class="!bg-ink-900/40 !border-white/10 !text-white" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="w-full justify-center py-3">{{ __('Send password reset link') }}</x-primary-button>
    </form>

    <p class="text-center text-xs text-ink-400 mt-6">
        <a href="{{ route('login') }}" class="text-field-400 font-bold hover:text-field-300">{{ __('Back to sign in') }}</a>
    </p>
</x-guest-layout>
