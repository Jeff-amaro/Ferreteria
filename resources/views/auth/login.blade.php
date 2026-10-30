@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">Inicia sesión</h2>
        <p class="mt-2 text-sm text-slate-500">Accede al panel de gestión de Ferretería EJJ.</p>
    </div>

    @if ($errors->any())
        <div class="mt-6"><x-alert type="error">{{ $errors->first() }}</x-alert></div>
    @endif

    @if (session('success'))
        <div class="mt-6"><x-alert type="success">{{ session('success') }}</x-alert></div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <x-input name="email" label="Correo electrónico" type="email" required autofocus placeholder="admin@ejj.pe" autocomplete="email" />
        <x-input name="password" label="Contraseña" type="password" required placeholder="••••••••" autocomplete="current-password" />

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                Recordarme
            </label>
            <span class="text-xs text-slate-400">Acceso seguro</span>
        </div>

        <button type="submit" class="btn-primary w-full py-3">Ingresar al sistema</button>
    </form>

    <div class="mt-8 rounded-2xl border border-primary-100 bg-primary-50/70 p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-primary-700">Credenciales de demostración</p>
        <div class="mt-2 grid gap-1 text-sm text-slate-600">
            <p><span class="font-medium text-slate-800">Administrador:</span> admin@ejj.pe / password</p>
            <p><span class="font-medium text-slate-800">Vendedor:</span> vendedor@ejj.pe / password</p>
            <p><span class="font-medium text-slate-800">Almacenero:</span> almacen@ejj.pe / password</p>
        </div>
    </div>

    <p class="mt-8 text-center text-sm text-slate-500">
        ¿Eres cliente nuevo?
        <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:text-primary-700">Crea tu cuenta</a>
    </p>
    <p class="mt-2 text-center text-sm">
        <a href="{{ route('catalog.index') }}" class="font-medium text-slate-500 hover:text-slate-700">Ver catálogo sin iniciar sesión</a>
    </p>
@endsection
