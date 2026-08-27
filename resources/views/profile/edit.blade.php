<x-app-layout>
    <div class="space-y-6 max-w-xl">
        <h1 class="text-xl font-bold text-ink-900">{{ __('Profile') }}</h1>

        <div class="bg-gradient-to-br from-field-600 to-field-800 rounded-3xl p-6 flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-white font-extrabold text-2xl shrink-0">
                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-base font-bold text-white">{{ $user->name }}</p>
                <p class="text-xs text-field-100 mt-0.5">
                    {{ match($user->role) {
                        'admin' => __('Administrator'),
                        'depot_manager' => __('Depot Manager'),
                        default => __('Field Coordinator'),
                    } }} · {{ $user->email }}
                </p>
            </div>
        </div>

        <div class="bg-white border border-ink-100 rounded-2xl p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-field-50 text-field-600 flex items-center justify-center"><x-icon name="user" class="w-4 h-4" /></div>
                <h2 class="text-sm font-bold text-ink-900">{{ __('Account details') }}</h2>
            </div>
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
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-amber-alert-50 text-amber-alert-600 flex items-center justify-center"><x-icon name="shield-check" class="w-4 h-4" /></div>
                <h2 class="text-sm font-bold text-ink-900">{{ __('Change Password') }}</h2>
            </div>
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
