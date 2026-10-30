@extends('layouts.app')

@section('title', 'Nuevo cliente')

@section('content')
    <x-page-header title="Nuevo cliente" subtitle="Registra los datos del cliente para cotizaciones y ventas.">
        <a href="{{ route('clients.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('clients.store') }}">
            @csrf
            @include('clients._form', ['client' => null])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('clients.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Guardar cliente</button>
            </div>
        </form>
    </div>
@endsection
