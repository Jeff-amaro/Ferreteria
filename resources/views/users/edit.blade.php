@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
    <x-page-header title="Editar usuario" subtitle="Actualiza los datos y permisos de {{ $user->name }}.">
        <a href="{{ route('users.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('users._form', ['user' => $user])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Actualizar usuario</button>
            </div>
        </form>
    </div>
@endsection
