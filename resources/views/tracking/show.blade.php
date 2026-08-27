<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReliefFlow — {{ __('Shipment Tracking') }}</title>

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-ink-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="w-11 h-11 rounded-2xl bg-field-500 flex items-center justify-center shadow-lg shadow-field-950/40">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span class="text-2xl font-bold text-white tracking-tight">ReliefFlow</span>
        </div>

        <div class="bg-ink-800/60 border border-white/5 rounded-3xl shadow-2xl shadow-black/40 p-8 space-y-6 backdrop-blur">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Manifest ID') }}</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $shipment->qr_code_token }}</p>
                </div>
                <img src="{{ $qrCode }}" alt="QR" class="w-16 h-16 rounded-lg bg-white p-1">
            </div>

            <div class="flex items-center gap-3 text-[11px] font-bold">
                <div class="flex items-center gap-1.5 text-field-400">
                    <span class="w-2 h-2 rounded-full bg-current"></span> {{ __('Dispatched') }}
                </div>
                <div class="flex-grow h-px bg-white/10"></div>
                <div class="flex items-center gap-1.5 {{ $shipment->status === 'delivered' ? 'text-field-400' : 'text-ink-500' }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> {{ __('Delivered') }}
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Origin Warehouse') }}</p>
                    <p class="font-bold text-white mt-0.5">{{ $shipment->warehouse->name }}</p>
                </div>
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Destination') }}</p>
                    <p class="font-bold text-white mt-0.5">{{ $shipment->aidRequest->location }}</p>
                </div>
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Driver') }}</p>
                    <p class="font-bold text-white mt-0.5">{{ $shipment->driver_name }}</p>
                </div>
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Status') }}</p>
                    <p class="font-bold text-white mt-0.5">
                        {{ $shipment->status === 'delivered' ? __('Delivered') : __('Dispatched') }}
                        @if($shipment->status === 'delivered')
                            · {{ $shipment->delivered_at->diffForHumans() }}
                        @endif
                    </p>
                </div>
            </div>

            <div>
                <p class="text-[11px] font-bold text-ink-400 mb-2">{{ __('Manifest Contents') }}</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($shipment->aidRequest->requestItems as $requestItem)
                        <span class="text-[10px] font-semibold bg-white/5 text-ink-200 border border-white/10 rounded-full px-2.5 py-1">
                            {{ $requestItem->item->name }} × {{ number_format($requestItem->quantity) }} {{ $requestItem->item->unit }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <p class="text-center text-[11px] text-ink-500 mt-6">{{ __('This is a public tracking page. No sensitive contact information is displayed.') }}</p>
    </div>
</body>
</html>
