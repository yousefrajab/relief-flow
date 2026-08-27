@props(['class' => ''])

<button
    type="button"
    onclick="var isDark = document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', isDark ? 'dark' : 'light');"
    aria-label="{{ __('Toggle dark mode') }}"
    {{ $attributes->merge(['class' => $class]) }}
>
    <x-icon name="moon" class="w-4 h-4 dark:hidden" />
    <x-icon name="sun" class="w-4 h-4 hidden dark:block" />
</button>
