<x-app-layout>
    <div class="space-y-6 max-w-4xl">
        <h1 class="text-xl font-bold text-ink-900">{{ __('Profile') }}</h1>

        <div class="bg-gradient-to-br from-field-600 to-field-800 rounded-3xl p-6 flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-white font-extrabold text-2xl shrink-0 overflow-hidden">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                @endif
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white border border-ink-100 rounded-2xl p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-field-50 text-field-600 flex items-center justify-center"><x-icon name="camera" class="w-4 h-4" /></div>
                        <h2 class="text-sm font-bold text-ink-900">{{ __('Profile Photo') }}</h2>
                    </div>

                    <div class="w-28 h-28 rounded-2xl bg-field-50 border border-ink-100 flex items-center justify-center text-field-600 font-extrabold text-3xl mx-auto overflow-hidden">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        @endif
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-3 mt-5">
                        @csrf @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="phone" value="{{ $user->phone }}">
                        <input type="file" name="avatar" accept="image/*" class="block w-full text-xs text-ink-600 file:me-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-field-50 file:text-field-700 file:text-xs file:font-bold">
                        <x-input-error :messages="$errors->get('avatar')" />
                        <p class="text-[10px] text-ink-400">{{ __('JPG, PNG, or WEBP. Max 2MB.') }}</p>
                        <x-primary-button class="w-full justify-center">{{ __('Upload photo') }}</x-primary-button>
                    </form>

                    @if($user->avatar_url)
                        <form method="POST" action="{{ route('profile.avatar.destroy') }}" class="mt-2" onsubmit="return confirm('{{ __('Remove your profile photo?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full text-center px-3 py-2 rounded-xl text-[11px] font-bold text-rose-600 hover:bg-rose-50 transition-colors">{{ __('Remove photo') }}</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-ink-100 rounded-2xl p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-field-50 text-field-600 flex items-center justify-center"><x-icon name="user" class="w-4 h-4" /></div>
                        <h2 class="text-sm font-bold text-ink-900">{{ __('Account details') }}</h2>
                    </div>
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label :value="__('Full name')" />
                                <x-text-input name="name" value="{{ old('name', $user->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>
                            <div>
                                <x-input-label :value="__('Phone number')" />
                                <x-text-input name="phone" value="{{ old('phone', $user->phone) }}" />
                                <x-input-error :messages="$errors->get('phone')" />
                            </div>
                        </div>
                        <div>
                            <x-input-label :value="__('Email address')" />
                            <x-text-input type="email" name="email" value="{{ old('email', $user->email) }}" required />
                            <x-input-error :messages="$errors->get('email')" />
                        </div>
                        <x-primary-button>{{ __('Save changes') }}</x-primary-button>
                    </form>
                </div>

                <div
                    class="bg-white border border-ink-100 rounded-2xl p-6 flex items-center justify-between gap-4"
                    x-data="{
                        enabled: false,
                        status: 'idle',
                        async init() {
                            if (!window.ReliefFlowPush.pushSupported()) {
                                this.status = 'unsupported';
                                return;
                            }
                            const sub = await window.ReliefFlowPush.currentPushSubscription();
                            this.enabled = !!sub;
                        },
                        async toggle() {
                            this.status = 'loading';
                            try {
                                if (this.enabled) {
                                    await window.ReliefFlowPush.unsubscribeFromPush();
                                    this.enabled = false;
                                } else {
                                    await window.ReliefFlowPush.subscribeToPush();
                                    this.enabled = true;
                                }
                                this.status = 'idle';
                            } catch (e) {
                                this.status = 'error';
                            }
                        },
                    }"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="enabled ? 'bg-field-50 text-field-600' : 'bg-ink-100 text-ink-400'">
                            <x-icon name="bell" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-ink-900">{{ __('Push Notifications') }}</p>
                            <p class="text-[11px] text-ink-500 mt-0.5" x-show="status !== 'unsupported'">{{ __('Get notified on this device even when ReliefFlow is closed.') }}</p>
                            <p class="text-[11px] text-ink-400 mt-0.5" x-show="status === 'unsupported'" x-cloak>{{ __('Not supported on this browser or device.') }}</p>
                            <p class="text-[11px] text-rose-600 mt-0.5" x-show="status === 'error'" x-cloak>{{ __('Could not enable notifications. Check your browser permissions.') }}</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        x-on:click="toggle()"
                        x-show="status !== 'unsupported'"
                        :disabled="status === 'loading'"
                        role="switch"
                        :aria-checked="enabled.toString()"
                        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors"
                        :class="enabled ? 'bg-field-600' : 'bg-ink-200'"
                    >
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="enabled ? 'translate-x-6 rtl:-translate-x-6' : 'translate-x-0.5'"></span>
                    </button>
                </div>

                <div class="bg-white border border-ink-100 rounded-2xl p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-amber-alert-50 text-amber-alert-600 flex items-center justify-center"><x-icon name="shield-check" class="w-4 h-4" /></div>
                        <h2 class="text-sm font-bold text-ink-900">{{ __('Change Password') }}</h2>
                    </div>
                    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                        </div>
                        <x-primary-button>{{ __('Update Password') }}</x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
