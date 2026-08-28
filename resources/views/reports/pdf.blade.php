<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #16211f; font-size: 11px; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}; }
        h1 { font-size: 20px; margin: 0; }
        .eyebrow { font-size: 9px; font-weight: bold; color: #147e63; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 4px; }
        .header { border-bottom: 2px solid #147e63; padding-bottom: 12px; margin-bottom: 20px; }
        .meta { font-size: 9px; color: #6b7a77; margin-top: 4px; }
        .narrative { background: #f0f9f6; border: 1px solid #cfe9df; border-radius: 6px; padding: 12px 14px; margin-bottom: 20px; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #d8dedd; padding: 6px 8px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; }
        th { background: #f2f4f3; font-size: 9px; text-transform: uppercase; }
        .stats { width: 100%; margin-bottom: 20px; }
        .stats td { border: none; padding: 4px 8px; }
        .stat-box { border: 1px solid #d8dedd; border-radius: 6px; padding: 10px; }
        .stat-label { font-size: 8px; font-weight: bold; color: #6b7a77; text-transform: uppercase; }
        .stat-value { font-size: 18px; font-weight: bold; margin-top: 2px; }
        .bar-track { background: #eef1f0; border-radius: 4px; height: 8px; }
        .bar-fill { background: #147e63; height: 8px; border-radius: 4px; }
        .section-title { font-size: 12px; font-weight: bold; margin: 0 0 10px; }
        .footer { margin-top: 24px; font-size: 8px; color: #9aa5a2; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <p class="eyebrow">ReliefFlow</p>
        <h1>{{ __('Humanitarian Impact Report') }}</h1>
        <p class="meta">{{ now()->translatedFormat('F j, Y') }}</p>
    </div>

    <div class="narrative">{{ $narrative }}</div>

    <table class="stats">
        <tr>
            <td width="25%">
                <div class="stat-box">
                    <p class="stat-label">{{ __('Delivered') }}</p>
                    <p class="stat-value">{{ number_format($stats['delivered_count']) }}</p>
                </div>
            </td>
            <td width="25%">
                <div class="stat-box">
                    <p class="stat-label">{{ __('In Transit') }}</p>
                    <p class="stat-value">{{ number_format($stats['active_count']) }}</p>
                </div>
            </td>
            <td width="25%">
                <div class="stat-box">
                    <p class="stat-label">{{ __('Pending') }}</p>
                    <p class="stat-value">{{ number_format($stats['pending_count']) }}</p>
                </div>
            </td>
            <td width="25%">
                <div class="stat-box">
                    <p class="stat-label">{{ __('Rejected') }}</p>
                    <p class="stat-value">{{ number_format($stats['rejected_count']) }}</p>
                </div>
            </td>
        </tr>
    </table>

    <p class="section-title">{{ __('Active warehouses') }}: {{ number_format($stats['warehouse_count']) }}</p>

    @if(!empty($stats['top_categories']))
        <p class="section-title">{{ __('Items delivered by category') }}</p>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Category') }}</th>
                    <th width="30%">{{ __('Quantity delivered') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $max = max($stats['top_categories']); @endphp
                @foreach($stats['top_categories'] as $category => $quantity)
                    <tr>
                        <td>
                            {{ $category }}
                            <div class="bar-track"><div class="bar-fill" style="width: {{ $max > 0 ? round($quantity / $max * 100) : 0 }}%"></div></div>
                        </td>
                        <td>{{ number_format($quantity) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="section-title">{{ __('Weekly activity (last 8 weeks)') }}</p>
    <table>
        <thead>
            <tr>
                <th>{{ __('Week starting') }}</th>
                <th>{{ __('Requests submitted') }}</th>
                <th>{{ __('Deliveries confirmed') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($weeklyTrend as $week)
                <tr>
                    <td>{{ $week['label'] }}</td>
                    <td>{{ number_format($week['requests']) }}</td>
                    <td>{{ number_format($week['deliveries']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">ReliefFlow — {{ __('Humanitarian Logistics Coordination Platform') }} — {{ __('Generated on') }} {{ now()->translatedFormat('F j, Y, g:i a') }}</p>
</body>
</html>
