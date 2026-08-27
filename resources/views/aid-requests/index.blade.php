<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-ink-900">{{ __('Aid Requests') }}</h1>
            @if(in_array(auth()->user()->role, ['admin', 'coordinator']))
                <a href="{{ route('aid-requests.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold"><x-icon name="plus" class="w-4 h-4" /> {{ __('New Aid Request') }}</a>
            @endif
        </div>

        @include('partials.aid-request-list', ['aidRequests' => $aidRequests])

        {{ $aidRequests->links() }}
    </div>
</x-app-layout>
