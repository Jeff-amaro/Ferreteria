@extends('layouts.app')

@section('title', 'Campañas de catálogo')

@section('content')
    <x-page-header title="Campañas de catálogo" subtitle="Envía el catálogo y promociones a los clientes registrados por correo electrónico.">
        <x-status-badge color="bg-sky-100 text-sky-700">{{ $recipients }} destinatarios con correo</x-status-badge>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <div class="card p-6">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Nueva campaña</h2>
            <form method="POST" action="{{ route('campaigns.store') }}" class="mt-4 grid gap-4">
                @csrf
                <x-input name="subject" label="Asunto" required placeholder="Catálogo de ofertas de la semana" />
                <div>
                    <label class="label" for="category_id">Categoría destacada</label>
                    <select name="category_id" id="category_id" class="input">
                        <option value="">Catálogo completo</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-textarea name="message" label="Mensaje" required rows="6" placeholder="Hola, te compartimos nuestro catálogo actualizado con precios especiales..." />
                <div class="rounded-xl bg-sky-50 p-4 text-xs text-sky-800">
                    El envío se realiza a todos los clientes activos con correo registrado. En entorno local los mensajes se registran en el log de Laravel.
                </div>
                <button type="submit" class="btn-primary"><x-icon name="mail" class="h-4 w-4" /> Enviar campaña</button>
            </form>
        </div>

        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Historial de campañas</h2>
            </div>

            @if ($campaigns->isEmpty())
                <x-empty-state title="Aún no se han enviado campañas" description="Completa el formulario para enviar el primer catálogo." icon="mail" />
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($campaigns as $campaign)
                        <div class="p-5">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $campaign->subject }}</p>
                                    <p class="text-xs text-slate-500">
                                        Enviada por {{ $campaign->user?->name }} el {{ $campaign->sent_at?->format('d/m/Y H:i') }}
                                        @if ($campaign->category)
                                            · Categoría: {{ $campaign->category->name }}
                                        @endif
                                    </p>
                                </div>
                                <x-status-badge color="bg-emerald-100 text-emerald-700">{{ $campaign->recipients_count }} destinatarios</x-status-badge>
                            </div>
                            <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $campaign->message }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 p-4">{{ $campaigns->links() }}</div>
            @endif
        </div>
    </div>
@endsection
