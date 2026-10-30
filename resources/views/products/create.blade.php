@extends('layouts.app')

@section('title', 'Registrar producto')

@section('content')
    <x-page-header title="Registrar producto" subtitle="Completa la ficha del nuevo artículo del catálogo.">
        <a href="{{ route('products.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-4xl p-6">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('products._form', ['product' => null])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('products.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Guardar producto</button>
            </div>
        </form>
    </div>
@endsection
