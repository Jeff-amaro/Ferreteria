@extends('layouts.app')

@section('title', 'Usuarios y perfiles')

@section('content')
    <x-page-header title="Usuarios y perfiles" subtitle="Gestiona el acceso del equipo y los roles del sistema.">
        <a href="{{ route('users.create') }}" class="btn-primary">
            <x-icon name="plus" class="h-4 w-4" /> Nuevo usuario
        </a>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, correo o DNI…" class="input pl-9">
                </div>
                <select name="role" class="input sm:w-52">
                    <option value="">Todos los roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-secondary">Filtrar</button>
                @if (request()->filled('search') || request()->filled('role'))
                    <a href="{{ route('users.index') }}" class="btn-ghost">Limpiar</a>
                @endif
            </form>
        </div>

        @if ($users->isEmpty())
            <x-empty-state title="No hay usuarios" description="Registra al personal de ventas, almacén o administración." icon="users" />
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>DNI</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">{{ $user->initials }}</span>
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><x-status-badge :color="$user->role->color()">{{ $user->role->label() }}</x-status-badge></td>
                                <td>{{ $user->dni ?: '—' }}</td>
                                <td>{{ $user->phone ?: '—' }}</td>
                                <td>
                                    <x-status-badge :color="$user->active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                                        {{ $user->active ? 'Activo' : 'Inactivo' }}
                                    </x-status-badge>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('users.edit', $user) }}" class="btn-ghost px-2.5 py-2" title="Editar">
                                            <x-icon name="edit" class="h-4 w-4" />
                                        </a>
                                        @if ($user->id !== auth()->id() && $user->active)
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('¿Desactivar este usuario?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-ghost px-2.5 py-2 text-rose-600 hover:bg-rose-50" title="Desactivar">
                                                    <x-icon name="trash" class="h-4 w-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-4 py-3">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
