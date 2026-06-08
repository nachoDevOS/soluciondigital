<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        @php
            $component   = $page['component'] ?? null;
            $isPublic    = in_array($component, ['public/Welcome', 'public/About', 'public/Contact']);
            $seoLogo     = asset('image/logo.png');
            $seoIcon     = asset('image/icono.png');
            $siteUrl     = url('/');

            $seoMeta = match ($component) {
                'public/About' => [
                    'title'       => 'Quiénes Somos | Solución Digital',
                    'description' => 'Conocé al equipo detrás de Solución Digital. Creamos sistemas SaaS, chatbots y software a medida para negocios de Bolivia y Latinoamérica.',
                    'keywords'    => 'solución digital equipo, empresa software Bolivia, desarrollo a medida, startup tecnología',
                    'url'         => url('/about'),
                ],
                'public/Contact' => [
                    'title'       => 'Contacto | Solución Digital',
                    'description' => 'Contactá a Solución Digital para implementar tu sistema SaaS, punto de venta, chatbot o desarrollo a medida. Diagnóstico gratuito en 15 minutos.',
                    'keywords'    => 'contacto solución digital, cotizar software, demo SaaS, WhatsApp soporte',
                    'url'         => url('/contact'),
                ],
                default => [
                    'title'       => 'Solución Digital | Sistemas SaaS, chatbot y software a medida',
                    'description' => 'Solución Digital crea y alquila sistemas SaaS, punto de venta, inventario, facturación, chatbot, automatizaciones y software a medida para negocios.',
                    'keywords'    => 'SaaS Bolivia, software a medida, punto de venta, chatbot WhatsApp, sistema inventario, facturación electrónica, software restaurante, farmacia, ferretería',
                    'url'         => url('/'),
                ],
            };

            $jsonLd = [
                '@context' => 'https://schema.org',
                '@graph'   => [
                    [
                        '@type'       => 'Organization',
                        '@id'         => $siteUrl . '#organization',
                        'name'        => 'Solución Digital',
                        'url'         => $siteUrl,
                        'logo'        => ['@type' => 'ImageObject', 'url' => $seoLogo],
                        'image'       => $seoLogo,
                        'description' => $seoMeta['description'],
                        'telephone'   => '+59177149775',
                        'contactPoint' => [
                            '@type'             => 'ContactPoint',
                            'telephone'         => '+59177149775',
                            'contactType'       => 'sales',
                            'availableLanguage' => 'Spanish',
                            'contactOption'     => 'TollFree',
                        ],
                        'sameAs' => [
                            'https://wa.me/59177149775',
                        ],
                    ],
                    [
                        '@type'           => 'WebSite',
                        '@id'             => $siteUrl . '#website',
                        'url'             => $siteUrl,
                        'name'            => 'Solución Digital',
                        'description'     => $seoMeta['description'],
                        'publisher'       => ['@id' => $siteUrl . '#organization'],
                        'inLanguage'      => 'es',
                    ],
                    [
                        '@type'           => 'WebPage',
                        '@id'             => $seoMeta['url'] . '#webpage',
                        'url'             => $seoMeta['url'],
                        'name'            => $seoMeta['title'],
                        'description'     => $seoMeta['description'],
                        'isPartOf'        => ['@id' => $siteUrl . '#website'],
                        'about'           => ['@id' => $siteUrl . '#organization'],
                        'inLanguage'      => 'es',
                    ],
                ],
            ];
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if ($isPublic)
            <title>{{ $seoMeta['title'] }}</title>
            <meta name="description"  content="{{ $seoMeta['description'] }}">
            <meta name="keywords"     content="{{ $seoMeta['keywords'] }}">
            <meta name="robots"       content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
            <meta name="author"       content="Solución Digital">
            <link rel="canonical"     href="{{ $seoMeta['url'] }}">

            {{-- Open Graph --}}
            <meta property="og:type"              content="website">
            <meta property="og:locale"            content="es_BO">
            <meta property="og:site_name"         content="Solución Digital">
            <meta property="og:title"             content="{{ $seoMeta['title'] }}">
            <meta property="og:description"       content="{{ $seoMeta['description'] }}">
            <meta property="og:url"               content="{{ $seoMeta['url'] }}">
            <meta property="og:image"             content="{{ $seoLogo }}">
            <meta property="og:image:secure_url"  content="{{ $seoLogo }}">
            <meta property="og:image:type"        content="image/png">
            <meta property="og:image:width"       content="1254">
            <meta property="og:image:height"      content="1254">
            <meta property="og:image:alt"         content="Logo de Solución Digital">

            {{-- Twitter Card --}}
            <meta name="twitter:card"        content="summary_large_image">
            <meta name="twitter:title"       content="{{ $seoMeta['title'] }}">
            <meta name="twitter:description" content="{{ $seoMeta['description'] }}">
            <meta name="twitter:image"       content="{{ $seoLogo }}">
            <meta name="twitter:image:alt"   content="Logo de Solución Digital">

            {{-- Schema.org microdata --}}
            <meta itemprop="name"        content="{{ $seoMeta['title'] }}">
            <meta itemprop="description" content="{{ $seoMeta['description'] }}">
            <meta itemprop="image"       content="{{ $seoLogo }}">

            {{-- JSON-LD --}}
            <script type="application/ld+json">@json($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)</script>
        @else
            <meta name="robots" content="noindex, nofollow">
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

                @if ($isPublic)
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
