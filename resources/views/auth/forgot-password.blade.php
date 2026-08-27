<x-guest-layout>
    <h2 class="text-lg font-bold text-ink-900 text-center mb-1">{{ __('Reset your password') }}</h2>
    <p class="text-xs text-ink-400 text-center mb-6">{{ __("Enter your email address and we'll send you a link to reset your password.") }}</p>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="w-full justify-center py-3">{{ __('Send password reset link') }}</x-primary-button>
    </form>

    <p class="text-center text-xs text-ink-400 mt-6">
        <a href="{{ route('login') }}" class="text-field-600 font-bold hover:text-field-700">{{ __('Back to sign in') }}</a>
    </p>
</x-guest-layout>
