<x-app-layout>
    <div class="space-y-8">
        <h1 class="text-xl font-bold text-ink-900">{{ __('Accounts') }}</h1>

        @if($pendingUsers->isNotEmpty())
            <section class="space-y-3">
                <p class="text-xs font-bold text-amber-alert-700">{{ __('Pending Approval') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($pendingUsers as $pending)
                        <div class="bg-white border border-amber-alert-200 rounded-2xl p-4 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-ink-900">{{ $pending->name }}</p>
                                <p class="text-[11px] text-ink-500">{{ $pending->email }} · {{ $pending->role === 'depot_manager' ? __('Depot Manager') : __('Field Coordinator') }}</p>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <form method="POST" action="{{ route('users.approve', $pending) }}">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-field-500 text-white hover:bg-field-600">{{ __('Approve') }}</button>
                                </form>
                                <form method="POST" action="{{ route('users.reject', $pending) }}">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-ink-100 text-ink-600 hover:bg-rose-50 hover:text-rose-600">{{ __('Reject') }}</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="space-y-3">
            <p class="text-xs font-bold text-ink-700">{{ __('All Accounts') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($allUsers as $member)
                    <div class="bg-white border border-ink-100 rounded-2xl p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-xs font-bold text-ink-900">{{ $member->name }}</p>
                            <x-status-badge :status="$member->status" />
                        </div>
                        <p class="text-[11px] text-ink-500 mt-1">{{ $member->email }}</p>
                        <p class="text-[11px] text-ink-400">{{ $member->role === 'depot_manager' ? __('Depot Manager') : __('Field Coordinator') }}</p>
                        @if($member->status === 'active')
                            <form method="POST" action="{{ route('users.reject', $member) }}" class="mt-2">
                                @csrf
                                <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">{{ __('Suspend') }}</button>
                            </form>
                        @elseif($member->status === 'suspended')
                            <form method="POST" action="{{ route('users.approve', $member) }}" class="mt-2">
                                @csrf
                                <button type="submit" class="text-[11px] font-bold text-field-600 hover:text-field-700">{{ __('Reactivate') }}</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
