<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">{{ __('Welcome back') }}</h1>
        <p class="text-sm text-ink-400 mt-1.5">{{ __('Sign in to access the coordination dashboard') }}</p>
    </div>

    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-bold rounded-xl px-4 py-3 mb-5">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-5" x-data="{ showPassword: false }">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-2" />
            <div class="relative">
                <x-icon name="mail" class="w-4.5 h-4.5 text-ink-500 absolute top-1/2 -translate-y-1/2 start-4 pointer-events-none" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="you@company.com" class="!bg-ink-900/60 !border-white/10 !text-white placeholder:!text-ink-500 !ps-11" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <x-input-label for="password" :value="__('Password')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-0" />
                <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-field-400 hover:text-field-300">{{ __('Forgot password?') }}</a>
            </div>
            <div class="relative">
                <x-icon name="lock" class="w-4.5 h-4.5 text-ink-500 absolute top-1/2 -translate-y-1/2 start-4 pointer-events-none" />
                <input
                    id="password" name="password" required
                    x-bind:type="showPassword ? 'text' : 'password'"
                    class="block w-full rounded-xl border-ink-200 text-sm shadow-sm focus:border-field-500 focus:ring-field-500 !bg-ink-900/60 !border-white/10 !text-white !ps-11 !pe-11"
                >
                <button type="button" x-on:click="showPassword = !showPassword" class="absolute top-1/2 -translate-y-1/2 end-4 text-ink-500 hover:text-ink-300 transition-colors">
                    <x-icon name="eye" class="w-4.5 h-4.5" x-show="!showPassword" />
                    <x-icon name="eye-slash" class="w-4.5 h-4.5" x-show="showPassword" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label class="flex items-center gap-2 text-xs text-ink-400">
            <input type="checkbox" name="remember" class="rounded border-white/20 bg-ink-900/60 text-field-500 focus:ring-field-500">
            {{ __('Remember me') }}
        </label>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-xl bg-gradient-to-r from-field-500 to-field-600 hover:from-field-400 hover:to-field-500 text-white text-sm font-bold shadow-lg shadow-field-950/40 transition-all">
            {{ __('Sign in') }} <x-icon name="arrow-right" class="w-4 h-4 rtl:rotate-180" />
        </button>
    </form>

    <p class="text-center text-xs text-ink-400 mt-6">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="text-field-400 font-bold hover:text-field-300">{{ __('Register') }}</a>
    </p>
</x-guest-layout>
