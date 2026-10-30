@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('content')
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">Crea tu cuenta</h2>
        <p class="mt-2 text-sm text-slate-500">Regístrate para consultar el catálogo y solicitar cotizaciones.</p>
    </div>

    @if ($errors->any())
        <div class="mt-6">
            <x-alert type="error">
                <ul class="list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <x-input name="name" label="Nombre completo" required placeholder="Juan Pérez" autocomplete="name" />
        <x-input name="email" label="Correo electrónico" type="email" required placeholder="juan@example.com" autocomplete="email" />
        <div class="grid gap-5 sm:grid-cols-2">
            <x-input name="dni" label="DNI" placeholder="8 dígitos" maxlength="8" inputmode="numeric" />
            <x-input name="phone" label="Teléfono" placeholder="987654321" autocomplete="tel" />
        </div>
        <x-input name="password" label="Contraseña" type="password" required placeholder="Mínimo 8 caracteres" autocomplete="new-password" />
        <x-input name="password_confirmation" label="Confirmar contraseña" type="password" required autocomplete="new-password" />

        <button type="submit" class="btn-primary w-full py-3">Crear cuenta</button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:text-primary-700">Inicia sesión</a>
    </p>
@endsection
