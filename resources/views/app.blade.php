<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#0ea5e9">
        <meta name="description" content="Projeto-DF - Sistema de Gestão">

        <title inertia>{{ config('app.name', 'Projeto-DF') }}</title>

        <!-- PWA -->
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/icon-192x192.png">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="Projeto-DF">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        <style>
            [x-cloak] { display: none !important; }
            .fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
            .fade-enter-from, .fade-leave-to { opacity: 0; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        @inertia

        <script>
            // Verificar se o app já está instalado
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                window.deferredPrompt = e;
            });

            // Notificar quando o app estiver pronto para uso offline
            window.addEventListener('appinstalled', () => {
                window.deferredPrompt = null;
                console.log('PWA instalado com sucesso!');
            });
        </script>
    </body>
</html>
