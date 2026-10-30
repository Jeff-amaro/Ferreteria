@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
    <x-page-header title="Nuevo usuario" subtitle="Crea una cuenta y asigna el rol correspondiente.">
        <a href="{{ route('users.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            @include('users._form', ['user' => null])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Guardar usuario</button>
            </div>
        </form>
    </div>
@endsection
