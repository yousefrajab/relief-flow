<x-app-layout>
    <div class="space-y-6 max-w-5xl">
        <div>
            <a href="{{ route('aid-requests.index') }}" class="text-[11px] font-bold text-ink-400 hover:text-ink-700">&larr; {{ __('Aid Requests') }}</a>
            <h1 class="text-xl font-bold text-ink-900 mt-1">{{ __('New Aid Request') }}</h1>
        </div>

        <div class="bg-white border border-ink-100 rounded-2xl p-6 sm:p-8"
             x-data="{ rows: [{ item_id: '', quantity: 1 }], location: @js(old('location', '')), queued: false }"
             x-on:location-picked.window="location = $event.detail">
            <div x-show="queued" x-cloak class="bg-field-50 border border-field-200 rounded-2xl p-6 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-field-100 text-field-600 flex items-center justify-center mx-auto"><x-icon name="check-circle" class="w-6 h-6" /></div>
                <p class="text-sm font-bold text-field-800">{{ __('Saved offline — this request will be submitted automatically once you\'re back online.') }}</p>
                <button type="button" x-on:click="queued = false" class="text-[11px] font-bold text-field-700 hover:text-field-800">{{ __('Submit another request') }}</button>
            </div>

            <form
                x-show="!queued"
                method="POST"
                action="{{ route('aid-requests.store') }}"
                class="space-y-6"
                x-on:submit.prevent="window.ReliefFlowOffline.submitFormOrQueue($event.target, @js(__('New Aid Request')))"
                x-on:reliefflow:queued="queued = true; $event.target.reset(); rows = [{ item_id: '', quantity: 1 }]; location = ''"
            >
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <x-input-label :value="__('Target distribution location')" />
                            <x-text-input name="location" x-model="location" required />
                            <x-input-error :messages="$errors->get('location')" />
                        </div>

                        <div>
                            <x-input-label :value="__('Notes (optional)')" />
                            <textarea name="notes" rows="5" class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">{{ old('notes') }}</textarea>
                            <p class="text-[10px] text-ink-400 mt-1">{{ __('These notes help our AI triage the urgency of the request.') }}</p>
                        </div>
                    </div>

                    <div>
                        <x-input-label :value="__('Map location (optional)')" />
                        <x-location-picker />
                    </div>
                </div>

                <div class="space-y-3 pt-2 border-t border-ink-100">
                    <x-input-label :value="__('Requested items')" class="!mb-0 pt-4" />
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="flex gap-2 items-start">
                            <select :name="`items[${index}][item_id]`" x-model="row.item_id" required class="flex-grow rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                                <option value="" disabled selected>{{ __('Select item') }}</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                @endforeach
                            </select>
                            <input type="number" :name="`items[${index}][quantity]`" x-model="row.quantity" min="1" required class="w-24 rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                            <button type="button" x-on:click="rows.length > 1 && rows.splice(index, 1)" class="shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-ink-100 text-ink-500 hover:bg-rose-50 hover:text-rose-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </template>
                    <button type="button" x-on:click="rows.push({ item_id: '', quantity: 1 })" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-field-600 hover:text-field-700"><x-icon name="plus" class="w-3.5 h-3.5" /> {{ __('Add another item') }}</button>
                </div>

                <x-primary-button class="w-full justify-center py-3">{{ __('Submit Request') }}</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
