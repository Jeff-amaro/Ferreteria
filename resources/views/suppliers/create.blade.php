@extends('layouts.app')

@section('title', 'Nuevo proveedor')

@section('content')
    <x-page-header title="Nuevo proveedor" subtitle="Registra los datos del proveedor y su plazo de entrega.">
        <a href="{{ route('suppliers.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf
            @include('suppliers._form', ['supplier' => null])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('suppliers.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Guardar proveedor</button>
            </div>
        </form>
    </div>
@endsection
