@props(['class' => 'w-full h-auto'])

<svg viewBox="0 0 560 460" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $class]) }}>
    <defs>
        <linearGradient id="heroBg" x1="0" y1="0" x2="560" y2="460" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#147e63" />
            <stop offset="1" stop-color="#0a4139" />
        </linearGradient>
        <pattern id="heroDots" width="26" height="26" patternUnits="userSpaceOnUse">
            <circle cx="1.5" cy="1.5" r="1.5" fill="#ffffff" fill-opacity="0.12" />
        </pattern>
    </defs>

    <rect width="560" height="460" rx="32" fill="url(#heroBg)" />
    <rect width="560" height="460" rx="32" fill="url(#heroDots)" />

    <circle cx="470" cy="70" r="90" fill="#ffffff" fill-opacity="0.05" />
    <circle cx="40" cy="400" r="70" fill="#ffffff" fill-opacity="0.05" />

    <!-- route from warehouse to destination pin -->
    <path d="M195 300 C 260 300, 260 180, 340 175 S 420 150, 452 140" stroke="#ffffff" stroke-opacity="0.5" stroke-width="3" stroke-dasharray="2 10" stroke-linecap="round" />

    <!-- warehouse -->
    <g>
        <rect x="55" y="255" width="150" height="115" rx="10" fill="#ffffff" />
        <polygon points="50,258 130,205 210,258" fill="#eefaf6" />
        <rect x="118" y="300" width="30" height="70" rx="3" fill="#0f6b5c" />
        <rect x="70" y="285" width="26" height="26" rx="3" fill="#a8e5cf" />
        <rect x="164" y="285" width="26" height="26" rx="3" fill="#a8e5cf" />
    </g>

    <!-- truck travelling the route -->
    <g transform="translate(300,192)">
        <rect x="0" y="10" width="52" height="28" rx="4" fill="#ffffff" />
        <path d="M52 16h16l12 12v10H52z" fill="#ffbe4f" />
        <circle cx="14" cy="42" r="8" fill="#0c1413" />
        <circle cx="66" cy="42" r="8" fill="#0c1413" />
        <circle cx="14" cy="42" r="3" fill="#c7d1cf" />
        <circle cx="66" cy="42" r="3" fill="#c7d1cf" />
    </g>

    <!-- destination pin -->
    <g transform="translate(452,96)">
        <path d="M0 44C0 44 26 26 26 8A13 13 0 100 8C0 26 0 44 0 44Z" fill="#ffa322" />
        <circle cx="13" cy="9" r="5.5" fill="#ffffff" />
    </g>

    <!-- floating relief package icons -->
    <g transform="translate(370,300) rotate(-8)">
        <rect width="46" height="46" rx="8" fill="#ffffff" />
        <path d="M0 16h46M23 16v30" stroke="#a8e5cf" stroke-width="3" />
    </g>
    <g transform="translate(430,340) rotate(10)">
        <rect width="30" height="30" rx="6" fill="#ffbe4f" />
        <path d="M0 10h30M15 10v20" stroke="#b45308" stroke-width="2" />
    </g>
    <g transform="translate(60,120) rotate(-6)">
        <rect width="34" height="34" rx="7" fill="#ffffff" fill-opacity="0.9" />
        <path d="M0 11h34M17 11v23" stroke="#a8e5cf" stroke-width="2.5" />
    </g>
</svg>
