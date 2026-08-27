<x-guest-layout>
    <h2 class="text-lg font-bold text-white text-center mb-1">{{ __('Choose a new password') }}</h2>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <x-input-label for="email" :value="__('Email address')" class="!text-ink-300" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $email)" required autofocus class="!bg-ink-900/40 !border-white/10 !text-white" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('New password')" class="!text-ink-300" />
            <x-text-input id="password" type="password" name="password" required class="!bg-ink-900/40 !border-white/10 !text-white" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm new password')" class="!text-ink-300" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required class="!bg-ink-900/40 !border-white/10 !text-white" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="w-full justify-center py-3">{{ __('Reset password') }}</x-primary-button>
    </form>
</x-guest-layout>
