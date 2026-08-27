<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">{{ __('Create an account') }}</h1>
        <p class="text-sm text-ink-400 mt-1.5">{{ __('New field and depot accounts require administrator approval') }}</p>
    </div>

    <form method="POST" action="{{ route('register.store') }}" class="space-y-5" x-data="{ showPassword: false, showConfirm: false }">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Full name')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-2" />
            <div class="relative">
                <x-icon name="user" class="w-4.5 h-4.5 text-ink-500 absolute top-1/2 -translate-y-1/2 start-4 pointer-events-none" />
                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus class="!bg-ink-900/60 !border-white/10 !text-white !ps-11" />
            </div>
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email address')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-2" />
            <div class="relative">
                <x-icon name="mail" class="w-4.5 h-4.5 text-ink-500 absolute top-1/2 -translate-y-1/2 start-4 pointer-events-none" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required placeholder="you@company.com" class="!bg-ink-900/60 !border-white/10 !text-white placeholder:!text-ink-500 !ps-11" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone number')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-2" />
            <div class="relative">
                <x-icon name="phone" class="w-4.5 h-4.5 text-ink-500 absolute top-1/2 -translate-y-1/2 start-4 pointer-events-none" />
                <x-text-input id="phone" type="text" name="phone" :value="old('phone')" class="!bg-ink-900/60 !border-white/10 !text-white !ps-11" />
            </div>
            <x-input-error :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="role" :value="__('Account type')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-2" />
            <select id="role" name="role" required class="block w-full rounded-xl border-white/10 bg-ink-900/60 text-sm text-white shadow-sm focus:border-field-500 focus:ring-field-500">
                <option value="" disabled selected>{{ __('Select your role') }}</option>
                <option value="depot_manager" {{ old('role') === 'depot_manager' ? 'selected' : '' }}>{{ __('Depot Manager') }}</option>
                <option value="coordinator" {{ old('role') === 'coordinator' ? 'selected' : '' }}>{{ __('Field Coordinator') }}</option>
            </select>
            <x-input-error :messages="$errors->get('role')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-2" />
            <div class="relative">
                <x-icon name="lock" class="w-4.5 h-4.5 text-ink-500 absolute top-1/2 -translate-y-1/2 start-4 pointer-events-none" />
                <input id="password" name="password" required x-bind:type="showPassword ? 'text' : 'password'" class="block w-full rounded-xl border-ink-200 text-sm shadow-sm focus:border-field-500 focus:ring-field-500 !bg-ink-900/60 !border-white/10 !text-white !ps-11 !pe-11">
                <button type="button" x-on:click="showPassword = !showPassword" class="absolute top-1/2 -translate-y-1/2 end-4 text-ink-500 hover:text-ink-300 transition-colors">
                    <x-icon name="eye" class="w-4.5 h-4.5" x-show="!showPassword" />
                    <x-icon name="eye-slash" class="w-4.5 h-4.5" x-show="showPassword" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" class="!text-[10px] !uppercase !tracking-wider !text-ink-400 !mb-2" />
            <div class="relative">
                <x-icon name="lock" class="w-4.5 h-4.5 text-ink-500 absolute top-1/2 -translate-y-1/2 start-4 pointer-events-none" />
                <input id="password_confirmation" name="password_confirmation" required x-bind:type="showConfirm ? 'text' : 'password'" class="block w-full rounded-xl border-ink-200 text-sm shadow-sm focus:border-field-500 focus:ring-field-500 !bg-ink-900/60 !border-white/10 !text-white !ps-11 !pe-11">
                <button type="button" x-on:click="showConfirm = !showConfirm" class="absolute top-1/2 -translate-y-1/2 end-4 text-ink-500 hover:text-ink-300 transition-colors">
                    <x-icon name="eye" class="w-4.5 h-4.5" x-show="!showConfirm" />
                    <x-icon name="eye-slash" class="w-4.5 h-4.5" x-show="showConfirm" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-xl bg-gradient-to-r from-field-500 to-field-600 hover:from-field-400 hover:to-field-500 text-white text-sm font-bold shadow-lg shadow-field-950/40 transition-all">
            {{ __('Create account') }} <x-icon name="arrow-right" class="w-4 h-4 rtl:rotate-180" />
        </button>
    </form>

    <p class="text-center text-xs text-ink-400 mt-6">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="text-field-400 font-bold hover:text-field-300">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>
