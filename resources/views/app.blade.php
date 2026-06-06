<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        @php
            $isWelcomePage = ($page['component'] ?? null) === 'Welcome';
            $seoTitle = 'Solución Digital | Sistemas SaaS, chatbot y software a medida';
            $seoDescription = 'Solución Digital crea y alquila sistemas SaaS, punto de venta, inventario, facturación, chatbot, automatizaciones y software a medida para negocios.';
            $seoUrl = $isWelcomePage ? url()->current() : url('/');
            $seoLogo = asset('image/logo.png');
            $seoIcon = asset('image/icono.png');
            $organizationJsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'Solución Digital',
                'url' => url('/'),
                'logo' => $seoLogo,
                'image' => $seoLogo,
                'description' => $seoDescription,
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'telephone' => '+59177149775',
                    'contactType' => 'customer support',
                    'availableLanguage' => 'es',
                ],
            ];
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if ($isWelcomePage)
            <meta name="description" content="{{ $seoDescription }}">
            <link rel="canonical" href="{{ $seoUrl }}">
            <meta property="og:type" content="website">
            <meta property="og:locale" content="es_BO">
            <meta property="og:site_name" content="Solución Digital">
            <meta property="og:title" content="{{ $seoTitle }}">
            <meta property="og:description" content="{{ $seoDescription }}">
            <meta property="og:url" content="{{ $seoUrl }}">
            <meta property="og:image" content="{{ $seoLogo }}">
            <meta property="og:image:secure_url" content="{{ $seoLogo }}">
            <meta property="og:image:type" content="image/png">
            <meta property="og:image:width" content="1254">
            <meta property="og:image:height" content="1254">
            <meta property="og:image:alt" content="Logo de Solución Digital">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="{{ $seoTitle }}">
            <meta name="twitter:description" content="{{ $seoDescription }}">
            <meta name="twitter:image" content="{{ $seoLogo }}">
            <meta name="twitter:image:alt" content="Logo de Solución Digital">
            <meta name="thumbnail" content="{{ $seoLogo }}">
            <meta itemprop="name" content="Solución Digital">
            <meta itemprop="description" content="{{ $seoDescription }}">
            <meta itemprop="image" content="{{ $seoLogo }}">
            <script type="application/ld+json">@json($organizationJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
        @endif

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }

                @if ($isWelcomePage)
                    document.documentElement.classList.add('app-loading');
                    window.setTimeout(function() {
                        document.documentElement.classList.add('app-ready');
                        document.documentElement.classList.remove('app-loading');
                    }, 3000);
                @endif
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }

            html.app-loading:not(.app-ready),
            html.app-loading:not(.app-ready) body {
                background-color: #0f172a;
            }

            html.app-loading:not(.app-ready) #app {
                opacity: 0;
            }

            html.app-ready #app {
                opacity: 1;
                transition: opacity 120ms ease;
            }
        </style>

        <link rel="icon" href="{{ $seoIcon }}" type="image/png" sizes="any">
        <link rel="shortcut icon" href="{{ $seoIcon }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ $seoIcon }}">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
