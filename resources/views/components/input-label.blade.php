@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-bold text-ink-700 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
