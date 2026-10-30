<!DOCTYPE html>
<html lang="es" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EJJ Ferretería') }} · @yield('title', 'Catálogo')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col font-sans text-slate-800">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-20 max-w-7xl items-center gap-6 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-600 text-sm font-bold text-white">EJJ</span>
                <span class="leading-tight">
                    <span class="block text-base font-bold text-slate-900">Ferretería EJJ</span>
                    <span class="block text-xs text-slate-500">Ventas e inventario</span>
                </span>
            </a>

            <nav class="ml-auto hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex">
                <a href="{{ route('home') }}" class="hover:text-primary-600">Inicio</a>
                <a href="{{ route('catalog.index') }}" class="hover:text-primary-600">Catálogo</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary">Ir al panel</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-primary-600">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="btn-primary">Crear cuenta</a>
                @endauth
            </nav>

            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn-primary ml-auto md:hidden">Acceder</a>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-slate-50">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <p class="text-lg font-bold text-slate-900">{{ config('company.name') }}</p>
                <p class="mt-2 text-sm text-slate-600">RUC {{ config('company.ruc') }}</p>
                <p class="text-sm text-slate-600">{{ config('company.address') }}</p>
            </div>
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-slate-500">Contacto</p>
                <p class="mt-2 text-sm text-slate-600">{{ config('company.phone') }}</p>
                <p class="text-sm text-slate-600">{{ config('company.email') }}</p>
                <p class="text-sm text-slate-600">{{ config('company.website') }}</p>
            </div>
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-slate-500">Atención</p>
                <p class="mt-2 text-sm text-slate-600">Lunes a sábado · 8:00 a 18:00</p>
                <p class="text-sm text-slate-600">Cotizaciones, ventas al por mayor y delivery.</p>
            </div>
        </div>
        <div class="border-t border-slate-200 py-4 text-center text-xs text-slate-500">
            © {{ date('Y') }} {{ config('company.name') }}. Todos los derechos reservados.
        </div>
    </footer>
</body>
</html>
