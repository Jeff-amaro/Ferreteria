@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <x-page-header title="Editar producto" subtitle="Actualiza precios, stock y especificaciones de {{ $product->name }}.">
        <a href="{{ route('products.show', $product) }}" class="btn-secondary">Ver ficha</a>
        <a href="{{ route('products.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-4xl p-6">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('products._form', ['product' => $product])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('products.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Actualizar producto</button>
            </div>
        </form>
    </div>
@endsection
