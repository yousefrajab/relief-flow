@props(['status'])

@php
    $styles = match($status) {
        'pending' => 'bg-amber-alert-50 text-amber-alert-700 border-amber-alert-200',
        'dispatched' => 'bg-sky-50 text-sky-700 border-sky-200',
        'delivered' => 'bg-field-50 text-field-700 border-field-200',
        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
        'active' => 'bg-field-50 text-field-700 border-field-200',
        'inactive' => 'bg-ink-100 text-ink-500 border-ink-200',
        'pending_verification' => 'bg-amber-alert-50 text-amber-alert-700 border-amber-alert-200',
        'suspended' => 'bg-rose-50 text-rose-700 border-rose-200',
        default => 'bg-ink-100 text-ink-600 border-ink-200',
    };

    $labels = [
        'pending' => __('Pending'),
        'dispatched' => __('Dispatched'),
        'delivered' => __('Delivered'),
        'rejected' => __('Rejected'),
        'active' => __('Active'),
        'inactive' => __('Inactive'),
        'pending_verification' => __('Pending Approval'),
        'suspended' => __('Suspended'),
    ];
@endphp

<span {{ $attributes->merge(['class' => "shrink-0 px-2.5 py-1 text-[10px] font-bold rounded-full border $styles"]) }}>
    {{ $labels[$status] ?? $status }}
</span>
