<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EJJ Ferretería') }} · @yield('title', 'Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans">
    <div class="min-h-full">
        <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-slate-900/40 lg:hidden" data-sidebar-toggle></div>

        @include('partials.sidebar')

        <div class="lg:pl-72">
            @include('partials.topbar')

            <main class="px-4 py-8 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    @if (session('success'))
                        <div class="mb-5"><x-alert type="success">{{ session('success') }}</x-alert></div>
                    @endif
                    @if (session('error'))
                        <div class="mb-5"><x-alert type="error">{{ session('error') }}</x-alert></div>
                    @endif
                    @if (session('warning'))
                        <div class="mb-5"><x-alert type="warning">{{ session('warning') }}</x-alert></div>
                    @endif
                    @if (session('info'))
                        <div class="mb-5"><x-alert type="info">{{ session('info') }}</x-alert></div>
                    @endif
                    @if ($errors->any() && ! isset($hideErrors))
                        <div class="mb-5">
                            <x-alert type="error">
                                <p class="font-semibold">Revisa la información ingresada:</p>
                                <ul class="mt-1 list-inside list-disc space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </x-alert>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
