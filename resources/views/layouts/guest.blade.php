<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EJJ Ferretería') }} · @yield('title', 'Acceso')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans">
    <div class="flex min-h-full">
        <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-primary-600 p-12 text-white lg:flex">
            <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-white/5"></div>

            <div class="relative">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-base font-bold text-primary-700">EJJ</span>
                    <span class="leading-tight">
                        <span class="block text-lg font-bold">Ferretería EJJ</span>
                        <span class="block text-sm text-white/70">Ventas e inventario</span>
                    </span>
                </a>
            </div>

            <div class="relative max-w-md">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/60">Sistema web</p>
                <h1 class="mt-4 text-4xl font-bold leading-tight">Gestiona ventas, stock y proveedores en un solo lugar.</h1>
                <p class="mt-4 text-sm leading-relaxed text-white/80">
                    Punto de venta con IGV, cotizaciones correlativas, control de inventario, auditorías y reposición automática para tu ferretería.
                </p>
                <div class="mt-8 grid grid-cols-2 gap-4 text-sm">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                        <p class="text-2xl font-bold">22</p>
                        <p class="text-white/70">Casos de uso implementados</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                        <p class="text-2xl font-bold">18%</p>
                        <p class="text-white/70">IGV calculado automáticamente</p>
                    </div>
                </div>
            </div>

            <p class="relative text-xs text-white/50">© {{ date('Y') }} EJJ Ferretería. Todos los derechos reservados.</p>
        </div>

        <div class="flex w-full flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-16">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <x-logo />
                </div>
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
