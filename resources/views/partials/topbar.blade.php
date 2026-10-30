@php
    $pendingAlerts = \App\Models\Alert::pending()->count();
@endphp

<header class="sticky top-0 z-20 flex h-20 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
    <button type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" data-sidebar-toggle aria-label="Abrir menú">
        <x-icon name="menu" class="h-6 w-6" />
    </button>

    <div class="min-w-0 flex-1">
        <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">EJJ / {{ auth()->user()->role->label() }}</p>
        <h2 class="truncate text-lg font-bold text-slate-900">@yield('title', 'Panel de control')</h2>
    </div>

    <a href="{{ route('alerts.index') }}" class="relative rounded-xl border border-slate-200 p-2.5 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700" aria-label="Alertas">
        <x-icon name="alert" class="h-5 w-5" />
        @if ($pendingAlerts > 0)
            <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white">{{ $pendingAlerts }}</span>
        @endif
    </a>

    <div class="hidden items-center gap-3 border-l border-slate-200 pl-4 sm:flex">
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700">
            {{ auth()->user()->initials }}
        </span>
        <div class="leading-tight">
            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
            <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
        </div>
    </div>
</header>
