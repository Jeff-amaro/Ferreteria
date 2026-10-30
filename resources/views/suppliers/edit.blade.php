@extends('layouts.app')

@section('title', 'Editar proveedor')

@section('content')
    <x-page-header title="Editar proveedor" subtitle="Actualiza los datos de {{ $supplier->business_name }}.">
        <a href="{{ route('suppliers.show', $supplier) }}" class="btn-secondary">Ver ficha</a>
        <a href="{{ route('suppliers.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-3xl p-6">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf
            @method('PUT')
            @include('suppliers._form', ['supplier' => $supplier])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('suppliers.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Actualizar proveedor</button>
            </div>
        </form>
    </div>
@endsection
