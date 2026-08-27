<x-guest-layout>
    <h2 class="text-lg font-bold text-ink-900 text-center mb-1">{{ __('Create an account') }}</h2>
    <p class="text-xs text-ink-400 text-center mb-6">{{ __('New field and depot accounts require administrator approval') }}</p>

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Full name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone number')" />
            <x-text-input id="phone" type="text" name="phone" :value="old('phone')" />
            <x-input-error :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="role" :value="__('Account type')" />
            <select id="role" name="role" required class="block w-full rounded-xl border-ink-200 bg-white text-sm text-ink-900 shadow-sm focus:border-field-500 focus:ring-field-500">
                <option value="" disabled selected>{{ __('Select your role') }}</option>
                <option value="depot_manager" {{ old('role') === 'depot_manager' ? 'selected' : '' }}>{{ __('Depot Manager') }}</option>
                <option value="coordinator" {{ old('role') === 'coordinator' ? 'selected' : '' }}>{{ __('Field Coordinator') }}</option>
            </select>
            <x-input-error :messages="$errors->get('role')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="w-full justify-center py-3">{{ __('Create account') }}</x-primary-button>
    </form>

    <p class="text-center text-xs text-ink-400 mt-6">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="text-field-600 font-bold hover:text-field-700">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>
