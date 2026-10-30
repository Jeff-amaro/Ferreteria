@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
    <x-page-header title="Mi perfil" subtitle="Actualiza tus datos personales y tu contraseña de acceso." />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-6 lg:col-span-1">
            <div class="flex items-center gap-4">
                <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-100 text-xl font-bold text-primary-700">
                    {{ $user->initials }}
                </span>
                <div>
                    <p class="text-lg font-bold text-slate-900">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                    <span class="badge mt-2 {{ $user->role->color() }}">{{ $user->role->label() }}</span>
                </div>
            </div>
            <dl class="mt-6 space-y-3 text-sm">
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3">
                    <dt class="text-slate-500">DNI</dt>
                    <dd class="font-medium text-slate-800">{{ $user->dni ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3">
                    <dt class="text-slate-500">Teléfono</dt>
                    <dd class="font-medium text-slate-800">{{ $user->phone ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3">
                    <dt class="text-slate-500">Estado</dt>
                    <dd><x-status-badge :color="$user->active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">{{ $user->active ? 'Activo' : 'Inactivo' }}</x-status-badge></dd>
                </div>
            </dl>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <form method="POST" action="{{ route('profile.update') }}" class="card p-6">
                @csrf
                @method('PUT')
                <h2 class="text-base font-bold text-slate-900">Datos personales</h2>
                <p class="mt-1 text-sm text-slate-500">Esta información se muestra en el sistema.</p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-input name="name" label="Nombre completo" :value="$user->name" required />
                    <x-input name="email" label="Correo electrónico" type="email" :value="$user->email" required />
                    <x-input name="dni" label="DNI" :value="$user->dni" maxlength="8" inputmode="numeric" />
                    <x-input name="phone" label="Teléfono" :value="$user->phone" />
                    <div class="sm:col-span-2">
                        <x-input name="address" label="Dirección" :value="$user->address" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary">Guardar cambios</button>
                </div>
            </form>

            <form method="POST" action="{{ route('profile.password') }}" class="card p-6">
                @csrf
                @method('PUT')
                <h2 class="text-base font-bold text-slate-900">Seguridad</h2>
                <p class="mt-1 text-sm text-slate-500">Cambia tu contraseña periódicamente.</p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input name="current_password" label="Contraseña actual" type="password" required autocomplete="current-password" />
                    </div>
                    <x-input name="password" label="Nueva contraseña" type="password" required autocomplete="new-password" />
                    <x-input name="password_confirmation" label="Confirmar nueva contraseña" type="password" required autocomplete="new-password" />
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-secondary">Actualizar contraseña</button>
                </div>
            </form>
        </div>
    </div>
@endsection
