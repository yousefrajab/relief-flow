@props(['title', 'subtitle'])

<div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-field-600 to-field-800 px-6 py-7 md:px-8 md:py-8">
    <div class="absolute -top-10 -end-10 w-48 h-48 rounded-full bg-white/5"></div>
    <div class="absolute -bottom-14 -start-6 w-40 h-40 rounded-full bg-white/5"></div>
    <div class="relative flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-white">{{ $title }}</h1>
            <p class="text-xs text-field-100 mt-1.5 max-w-md leading-relaxed">{{ $subtitle }}</p>
        </div>
        @isset($action)
            <div>{{ $action }}</div>
        @endisset
    </div>
</div>
