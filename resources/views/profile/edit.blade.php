<x-app-layout>
    <div class="space-y-6 max-w-xl">
        <h1 class="text-xl font-bold text-ink-900">{{ __('Profile') }}</h1>

        <div class="bg-white border border-ink-100 rounded-2xl p-6">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <x-input-label :value="__('Full name')" />
                    <x-text-input name="name" value="{{ old('name', $user->name) }}" required />
                    <x-input-error :messages="$errors->get('name')" />
                </div>
                <div>
                    <x-input-label :value="__('Email address')" />
                    <x-text-input type="email" name="email" value="{{ old('email', $user->email) }}" required />
                    <x-input-error :messages="$errors->get('email')" />
                </div>
                <div>
                    <x-input-label :value="__('Phone number')" />
                    <x-text-input name="phone" value="{{ old('phone', $user->phone) }}" />
                    <x-input-error :messages="$errors->get('phone')" />
                </div>
                <x-primary-button>{{ __('Save changes') }}</x-primary-button>
            </form>
        </div>

        <div class="bg-white border border-ink-100 rounded-2xl p-6">
            <h2 class="text-sm font-bold text-ink-900 mb-4">{{ __('Change Password') }}</h2>
            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <x-input-label :value="__('Current password')" />
                    <x-text-input type="password" name="current_password" required />
                    <x-input-error :messages="$errors->get('current_password')" />
                </div>
                <div>
                    <x-input-label :value="__('New password')" />
                    <x-text-input type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" />
                </div>
                <div>
                    <x-input-label :value="__('Confirm new password')" />
                    <x-text-input type="password" name="password_confirmation" required />
                </div>
                <x-primary-button>{{ __('Update Password') }}</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
