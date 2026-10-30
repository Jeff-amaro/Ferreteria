@extends('layouts.app')

@section('title', 'Nueva categoría')

@section('content')
    <x-page-header title="Nueva categoría" subtitle="Agrupa productos para facilitar la búsqueda en el catálogo.">
        <a href="{{ route('categories.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            @include('categories._form', ['category' => null])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('categories.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Guardar categoría</button>
            </div>
        </form>
    </div>
@endsection
