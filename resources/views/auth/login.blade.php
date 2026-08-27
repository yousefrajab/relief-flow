<x-guest-layout>
    <h2 class="text-lg font-bold text-white text-center mb-1">{{ __('Welcome back') }}</h2>
    <p class="text-xs text-ink-400 text-center mb-6">{{ __('Sign in to access the coordination dashboard') }}</p>

    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-bold rounded-xl px-4 py-3 mb-4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" class="!text-ink-300" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus class="!bg-ink-900/40 !border-white/10 !text-white" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="!text-ink-300" />
            <x-text-input id="password" type="password" name="password" required class="!bg-ink-900/40 !border-white/10 !text-white" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-xs text-ink-300">
                <input type="checkbox" name="remember" class="rounded border-white/20 bg-ink-900/40 text-field-500 focus:ring-field-500">
                {{ __('Remember me') }}
            </label>
            <a href="{{ route('password.request') }}" class="text-xs font-bold text-field-400 hover:text-field-300">{{ __('Forgot password?') }}</a>
        </div>

        <x-primary-button class="w-full justify-center py-3">{{ __('Sign in') }}</x-primary-button>
    </form>

    <p class="text-center text-xs text-ink-400 mt-6">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="text-field-400 font-bold hover:text-field-300">{{ __('Register') }}</a>
    </p>
</x-guest-layout>
