<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #16211f; font-size: 11px; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}; }
        h1 { font-size: 18px; margin: 4px 0 0; }
        .eyebrow { font-size: 9px; font-weight: bold; color: #147e63; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
        .header { border-bottom: 2px solid #147e63; padding-bottom: 12px; margin-bottom: 4px; }
        .confirmed { display: inline-block; margin-top: 10px; padding: 4px 10px; background: #e6f6ee; color: #147e63; border-radius: 4px; font-size: 10px; font-weight: bold; }
        table.layout { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.layout td { border: none; padding: 0; vertical-align: top; }
        .field-label { font-size: 8px; font-weight: bold; color: #6b7a77; text-transform: uppercase; margin: 0; }
        .field-value { font-size: 11px; font-weight: bold; margin: 2px 0 12px; }
        table.manifest { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.manifest th, table.manifest td { border: 1px solid #d8dedd; padding: 6px 8px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; }
        table.manifest th { background: #f2f4f3; font-size: 9px; text-transform: uppercase; }
        .verification { margin-top: 16px; padding: 10px 12px; border-radius: 6px; font-size: 10px; }
        .verification.verified { background: #e6f6ee; color: #0f5f49; }
        .verification.needs_review { background: #fef3e2; color: #92600a; }
        .section-title { font-size: 11px; font-weight: bold; margin: 18px 0 6px; }
        .signatures { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .signatures td { border: none; padding: 0 12px; text-align: center; font-size: 9px; font-weight: bold; color: #6b7a77; }
        .sig-line { border-top: 1px solid #b7c1be; padding-top: 6px; margin-top: 30px; }
        .footer { margin-top: 30px; font-size: 8px; color: #9aa5a2; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <p class="eyebrow">ReliefFlow</p>
        <h1>{{ __('Official Delivery Receipt') }}</h1>
        <div class="confirmed">&#10003; {{ __('Delivered') }} &middot; {{ $shipment->delivered_at->translatedFormat('F j, Y, g:i a') }}</div>
    </div>

    <table class="layout">
        <tr>
            <td width="50%">
                <p class="field-label">{{ __('Manifest ID') }}</p>
                <p class="field-value">{{ $shipment->qr_code_token }}</p>

                <p class="field-label">{{ __('Origin Warehouse') }}</p>
                <p class="field-value">{{ $shipment->warehouse->name }}</p>

                <p class="field-label">{{ __('Driver') }}</p>
                <p class="field-value">{{ $shipment->driver_name }} &mdash; {{ $shipment->driver_phone }}</p>
            </td>
            <td width="50%">
                <p class="field-label">{{ __('Destination') }}</p>
                <p class="field-value">{{ $shipment->aidRequest->location }}</p>

                <p class="field-label">{{ __('Field Coordinator') }}</p>
                <p class="field-value">{{ $shipment->aidRequest->user->name }}</p>

                <p class="field-label">{{ __('Dispatched') }}</p>
                <p class="field-value">{{ optional($shipment->picked_up_at ?? $shipment->created_at)->translatedFormat('F j, Y, g:i a') }}</p>
            </td>
        </tr>
    </table>

    <p class="section-title">{{ __('Manifest Contents') }}</p>
    <table class="manifest">
        <thead>
            <tr>
                <th>{{ __('Item') }}</th>
                <th width="30%">{{ __('Quantity') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipment->aidRequest->requestItems as $requestItem)
                <tr>
                    <td>{{ $requestItem->item->name }}</td>
                    <td>{{ number_format($requestItem->quantity) }} {{ $requestItem->item->unit }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($shipment->ai_verification_status)
        <div class="verification {{ $shipment->ai_verification_status }}">
            <strong>{{ __('AI verification: :status', ['status' => $shipment->ai_verification_status]) }}</strong>
            @if($shipment->ai_verification_notes)
                <br>{{ $shipment->ai_verification_notes }}
            @endif
        </div>
    @endif

    <table class="signatures">
        <tr>
            <td width="50%"><div class="sig-line">{{ __('Driver Signature') }}</div></td>
            <td width="50%"><div class="sig-line">{{ __('Recipient Signature') }}</div></td>
        </tr>
    </table>

    <p class="footer">ReliefFlow — {{ __('Humanitarian Logistics Coordination Platform') }} — {{ __('Generated on') }} {{ now()->translatedFormat('F j, Y, g:i a') }}</p>
</body>
</html>
