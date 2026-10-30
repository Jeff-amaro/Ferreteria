@extends('layouts.app')

@section('title', 'Editar cliente')

@section('content')
    <x-page-header title="Editar cliente" subtitle="Actualiza los datos de {{ $client->name }}.">
        <a href="{{ route('clients.show', $client) }}" class="btn-secondary">Ver ficha</a>
        <a href="{{ route('clients.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('clients.update', $client) }}">
            @csrf
            @method('PUT')
            @include('clients._form', ['client' => $client])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('clients.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Actualizar cliente</button>
            </div>
        </form>
    </div>
@endsection
