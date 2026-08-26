<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-ink-100 hover:bg-ink-200 text-ink-700 text-xs font-bold rounded-xl transition-colors']) }}>
    {{ $slot }}
</button>
