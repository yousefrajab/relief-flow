<x-guest-layout>
    <h2 class="text-lg font-bold text-ink-900 text-center mb-1">{{ __('Welcome back') }}</h2>
    <p class="text-xs text-ink-400 text-center mb-6">{{ __('Sign in to access the coordination dashboard') }}</p>

    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-xl px-4 py-3 mb-4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-xs text-ink-500">
                <input type="checkbox" name="remember" class="rounded border-ink-300 text-field-600 focus:ring-field-500">
                {{ __('Remember me') }}
            </label>
            <a href="{{ route('password.request') }}" class="text-xs font-bold text-field-600 hover:text-field-700">{{ __('Forgot password?') }}</a>
        </div>

        <x-primary-button class="w-full justify-center py-3">{{ __('Sign in') }}</x-primary-button>
    </form>

    <p class="text-center text-xs text-ink-400 mt-6">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="text-field-600 font-bold hover:text-field-700">{{ __('Register') }}</a>
    </p>
</x-guest-layout>
