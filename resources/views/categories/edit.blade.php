@extends('layouts.app')

@section('title', 'Editar categoría')

@section('content')
    <x-page-header title="Editar categoría" subtitle="Actualiza la información de {{ $category->name }}.">
        <a href="{{ route('categories.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="card max-w-2xl p-6">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')
            @include('categories._form', ['category' => $category])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('categories.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Actualizar categoría</button>
            </div>
        </form>
    </div>
@endsection
