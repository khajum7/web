<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Junior Jazz') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" crossorigin>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        <svg class="fixed bottom-0 right-0" width="657" height="982" viewBox="0 0 657 982" fill="none" xmlns="http://www.w3.org/2000/svg" class="absolute bottom-0 right-0">
            <g opacity="0.2" filter="url(#filter0_f_7_29)">
                <path d="M435.511 313L656.511 982L320 797L435.511 313Z" fill="#3F2782" />
            </g>
            <defs>
                <filter id="filter0_f_7_29" x="0" y="-7" width="976.511" height="1309" filterUnits="userSpaceOnUse"
                    color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                    <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                    <feGaussianBlur stdDeviation="160" result="effect1_foregroundBlur_7_29" />
                </filter>
            </defs>
        </svg>
    </body>
</html>
