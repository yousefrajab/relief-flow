<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReliefFlow — {{ $shipment->qr_code_token }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-ink-50 p-8">
    <div class="max-w-3xl mx-auto space-y-4">
        <div class="no-print flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="text-xs font-bold text-ink-500 hover:text-ink-800">&larr; {{ __('Back to Dashboard') }}</a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-field-600 text-white text-xs font-bold hover:bg-field-700">{{ __('Print / Save as PDF') }}</button>
        </div>

        <div class="bg-white border-2 border-ink-900 rounded-3xl p-10 space-y-8">
            <div class="flex items-start justify-between border-b-2 border-dashed border-ink-200 pb-6">
                <div>
                    <p class="text-xs font-bold text-field-600 uppercase tracking-widest">ReliefFlow</p>
                    <h1 class="text-xl font-extrabold text-ink-900 mt-1">{{ __('Dispatch Manifest') }}</h1>
                </div>
                <img src="{{ $qrCode }}" alt="QR" class="w-24 h-24">
            </div>

            <div class="grid grid-cols-2 gap-6 text-xs">
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Manifest ID') }}</p>
                    <p class="font-bold text-ink-900 mt-0.5">{{ $shipment->qr_code_token }}</p>
                </div>
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Status') }}</p>
                    <p class="font-bold text-ink-900 mt-0.5">
                        {{ $shipment->status === 'delivered' ? __('Delivered') : __('Dispatched') }}
                    </p>
                </div>
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Origin Warehouse') }}</p>
                    <p class="font-bold text-ink-900 mt-0.5">{{ $shipment->warehouse->name }}</p>
                </div>
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Destination') }}</p>
                    <p class="font-bold text-ink-900 mt-0.5">{{ $shipment->aidRequest->location }}</p>
                </div>
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Driver') }}</p>
                    <p class="font-bold text-ink-900 mt-0.5">{{ $shipment->driver_name }} — {{ $shipment->driver_phone }}</p>
                </div>
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Field Coordinator') }}</p>
                    <p class="font-bold text-ink-900 mt-0.5">{{ $shipment->aidRequest->user->name }}</p>
                </div>
            </div>

            <div>
                <p class="text-xs font-bold text-ink-700 mb-2">{{ __('Manifest Contents') }}</p>
                <table class="w-full text-xs border border-ink-900">
                    <thead>
                        <tr class="bg-ink-100">
                            <th class="border border-ink-900 px-3 py-2 text-start">{{ __('Item') }}</th>
                            <th class="border border-ink-900 px-3 py-2 text-start">{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shipment->aidRequest->requestItems as $requestItem)
                            <tr>
                                <td class="border border-ink-900 px-3 py-2">{{ $requestItem->item->name }}</td>
                                <td class="border border-ink-900 px-3 py-2">{{ number_format($requestItem->quantity) }} {{ $requestItem->item->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-2 gap-12 pt-10 text-xs font-bold text-ink-500">
                <div class="border-t border-dashed border-ink-300 pt-2 text-center">{{ __('Driver Signature') }}</div>
                <div class="border-t border-dashed border-ink-300 pt-2 text-center">{{ __('Recipient Signature') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
