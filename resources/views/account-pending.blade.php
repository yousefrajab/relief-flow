<x-guest-layout>
    <div class="text-center space-y-4">
        <div class="w-14 h-14 rounded-2xl bg-amber-alert-500/15 border border-amber-alert-500/20 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7 text-amber-alert-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h2 class="text-lg font-bold text-white">
            @if(auth()->user()->status === 'suspended')
                {{ __('Your account has been suspended') }}
            @else
                {{ __('Your account is pending approval') }}
            @endif
        </h2>

        <p class="text-xs text-ink-400 leading-relaxed">
            @if(auth()->user()->status === 'suspended')
                {{ __('An administrator has suspended this account. Please contact your organization for assistance.') }}
            @else
                {{ __('An administrator needs to review and approve your account before you can access the dashboard. You will be able to sign in as soon as it is approved.') }}
            @endif
        </p>

        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <x-secondary-button type="submit" class="!bg-white/5 !text-ink-300 hover:!bg-white/10">
                {{ __('Log out') }}
            </x-secondary-button>
        </form>
    </div>
</x-guest-layout>
