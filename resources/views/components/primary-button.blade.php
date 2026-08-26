<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-field-600 hover:bg-field-700 text-white text-xs font-bold rounded-xl shadow-sm shadow-field-950/10 transition-colors']) }}>
    {{ $slot }}
</button>
