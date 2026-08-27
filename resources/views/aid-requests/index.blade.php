<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold text-ink-900">{{ __('Aid Requests') }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('aid-requests.export', $filters) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-ink-200 hover:border-ink-300 text-ink-700 text-xs font-bold"><x-icon name="chart" class="w-4 h-4" /> {{ __('Export CSV') }}</a>
                @if(in_array(auth()->user()->role, ['admin', 'coordinator']))
                    <a href="{{ route('aid-requests.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold"><x-icon name="plus" class="w-4 h-4" /> {{ __('New Aid Request') }}</a>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('aid-requests.index') }}" class="bg-white border border-ink-100 rounded-2xl p-4 flex flex-wrap items-end gap-3">
            <div class="flex-grow min-w-[180px]">
                <x-input-label :value="__('Search')" class="mb-1" />
                <div class="relative">
                    <x-icon name="search" class="w-4 h-4 text-ink-300 absolute top-1/2 -translate-y-1/2 start-3 pointer-events-none" />
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Location or coordinator name') }}" class="w-full ps-9 rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                </div>
            </div>
            <div>
                <x-input-label :value="__('Status')" class="mb-1" />
                <select name="status" class="rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach(['pending', 'dispatched', 'delivered', 'rejected'] as $status)
                        <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>
                            {{ match($status) { 'pending' => __('Pending'), 'dispatched' => __('Dispatched'), 'delivered' => __('Delivered'), 'rejected' => __('Rejected') } }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label :value="__('Priority')" class="mb-1" />
                <select name="priority" class="rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                    <option value="">{{ __('All priorities') }}</option>
                    @foreach(['normal', 'high', 'critical'] as $priority)
                        <option value="{{ $priority }}" {{ ($filters['priority'] ?? '') === $priority ? 'selected' : '' }}>
                            {{ match($priority) { 'normal' => __('Normal'), 'high' => __('High'), 'critical' => __('Critical') } }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-ink-900 text-white text-xs font-bold hover:bg-ink-800">{{ __('Apply filters') }}</button>
                @if(($filters['status'] ?? '') || ($filters['priority'] ?? '') || ($filters['search'] ?? ''))
                    <a href="{{ route('aid-requests.index') }}" class="px-3 py-2 rounded-xl text-ink-500 text-xs font-bold hover:text-ink-700">{{ __('Clear') }}</a>
                @endif
            </div>
        </form>

        @include('partials.aid-request-list', ['aidRequests' => $aidRequests])

        {{ $aidRequests->links() }}
    </div>
</x-app-layout>
