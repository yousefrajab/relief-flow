<x-app-layout>
    <div class="space-y-6 max-w-6xl">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-ink-900">{{ __('Humanitarian Impact Report') }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-ink-200 hover:border-ink-300 text-ink-700 text-xs font-bold"><x-icon name="chart" class="w-4 h-4" /> {{ __('Export CSV') }}</a>
                <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-white border border-ink-200 hover:border-ink-300 text-ink-700 text-xs font-bold">{{ __('Print / Save as PDF') }}</button>
                <a href="{{ route('reports.export-pdf') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold"><x-icon name="download" class="w-4 h-4" /> {{ __('Download PDF') }}</a>
            </div>
        </div>

        <div class="bg-white border border-ink-100 rounded-2xl p-6 space-y-2">
            <p class="text-[10px] font-bold text-field-600 uppercase tracking-wide">{{ __('AI-generated summary') }}</p>
            <p class="text-sm text-ink-800 leading-relaxed">{{ $narrative }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="grid grid-cols-2 gap-4 md:col-span-2">
                <div class="bg-white border border-ink-100 rounded-2xl p-5">
                    <div class="w-9 h-9 rounded-xl bg-field-50 text-field-600 flex items-center justify-center mb-3"><x-icon name="check-circle" class="w-4.5 h-4.5" /></div>
                    <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Delivered') }}</p>
                    <p class="text-2xl font-extrabold text-field-600 mt-1">{{ number_format($stats['delivered_count']) }}</p>
                </div>
                <div class="bg-white border border-ink-100 rounded-2xl p-5">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center mb-3"><x-icon name="truck" class="w-4.5 h-4.5" /></div>
                    <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('In Transit') }}</p>
                    <p class="text-2xl font-extrabold text-sky-600 mt-1">{{ number_format($stats['active_count']) }}</p>
                </div>
                <div class="bg-white border border-ink-100 rounded-2xl p-5">
                    <div class="w-9 h-9 rounded-xl bg-amber-alert-50 text-amber-alert-600 flex items-center justify-center mb-3"><x-icon name="exclamation" class="w-4.5 h-4.5" /></div>
                    <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Pending') }}</p>
                    <p class="text-2xl font-extrabold text-amber-alert-600 mt-1">{{ number_format($stats['pending_count']) }}</p>
                </div>
                <div class="bg-white border border-ink-100 rounded-2xl p-5">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mb-3"><x-icon name="trash" class="w-4.5 h-4.5" /></div>
                    <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Rejected') }}</p>
                    <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ number_format($stats['rejected_count']) }}</p>
                </div>
            </div>

            <div
                class="bg-white border border-ink-100 rounded-2xl p-5 flex flex-col items-center justify-center"
                x-data="{
                    init() {
                        new Chart(this.$refs.canvas, {
                            type: 'doughnut',
                            data: {
                                labels: [@js(__('Delivered')), @js(__('In Transit')), @js(__('Pending')), @js(__('Rejected'))],
                                datasets: [{
                                    data: [{{ $stats['delivered_count'] }}, {{ $stats['active_count'] }}, {{ $stats['pending_count'] }}, {{ $stats['rejected_count'] }}],
                                    backgroundColor: ['#147e63', '#0284c7', '#f88a0b', '#e11d48'],
                                    borderWidth: 0,
                                }],
                            },
                            options: {
                                plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 10 } } } },
                                cutout: '65%',
                            },
                        });
                    }
                }"
            >
                <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide self-start mb-2">{{ __('Request status breakdown') }}</p>
                <canvas x-ref="canvas" width="180" height="180"></canvas>
            </div>
        </div>

        <div
            class="bg-white border border-ink-100 rounded-2xl p-6"
            x-data="{
                init() {
                    new Chart(this.$refs.trend, {
                        type: 'line',
                        data: {
                            labels: @js(collect($weeklyTrend)->pluck('label')),
                            datasets: [
                                {
                                    label: @js(__('Requests submitted')),
                                    data: @js(collect($weeklyTrend)->pluck('requests')),
                                    borderColor: '#147e63',
                                    backgroundColor: 'rgba(20, 126, 99, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                },
                                {
                                    label: @js(__('Deliveries confirmed')),
                                    data: @js(collect($weeklyTrend)->pluck('deliveries')),
                                    borderColor: '#0284c7',
                                    backgroundColor: 'rgba(2, 132, 199, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                },
                            ],
                        },
                        options: {
                            plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 10 } } } },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } },
                                x: { ticks: { font: { size: 10 } } },
                            },
                        },
                    });
                }
            }"
        >
            <p class="text-[11px] font-bold text-ink-500 mb-3">{{ __('Weekly activity (last 8 weeks)') }}</p>
            <canvas x-ref="trend" height="90"></canvas>
        </div>

        @if($topCategories->isNotEmpty())
            <div class="bg-white border border-ink-100 rounded-2xl p-6">
                <p class="text-xs font-bold text-ink-700 mb-3">{{ __('Items delivered by category') }}</p>
                <div class="space-y-2">
                    @php $max = $topCategories->max(); @endphp
                    @foreach($topCategories as $category => $quantity)
                        <div>
                            <div class="flex items-center justify-between text-[11px] font-semibold text-ink-600 mb-1">
                                <span>{{ $category }}</span>
                                <span>{{ number_format($quantity) }}</span>
                            </div>
                            <div class="h-2 bg-ink-100 rounded-full overflow-hidden">
                                <div class="h-full bg-field-500" style="width: {{ $max > 0 ? round($quantity / $max * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
