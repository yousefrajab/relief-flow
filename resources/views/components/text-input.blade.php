@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full rounded-xl border-ink-200 bg-white text-sm text-ink-900 shadow-sm focus:border-field-500 focus:ring-field-500']) !!}>
