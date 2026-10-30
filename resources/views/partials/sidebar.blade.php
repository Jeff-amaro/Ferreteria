@php
    $user = auth()->user();
    $pendingAlerts = \App\Models\Alert::pending()->count();
    $lowStock = \App\Models\Product::active()->lowStock()->count();
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0">
    <div class="flex h-20 items-center justify-between border-b border-slate-100 px-5">
        <x-logo />
        <button type="button" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" data-sidebar-toggle aria-label="Cerrar menú">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        <div>
            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-widest text-slate-400">Principal</p>
            <div class="space-y-1">
                <x-nav-link route="dashboard" icon="dashboard">Dashboard</x-nav-link>
                <x-nav-link route="catalog.index" icon="home">Catálogo público</x-nav-link>
            </div>
        </div>

        @if ($user->hasRole(['administrador', 'vendedor']))
            <div>
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-widest text-slate-400">Ventas</p>
                <div class="space-y-1">
                    <x-nav-link route="pos.index" icon="cart">Punto de venta</x-nav-link>
                    <x-nav-link route="quotations.index" icon="document">Cotizaciones</x-nav-link>
                    <x-nav-link route="orders.index" icon="receipt">Órdenes de venta</x-nav-link>
                    <x-nav-link route="wholesale.index" icon="users">Solicitudes mayoristas</x-nav-link>
                    <x-nav-link route="campaigns.index" icon="mail">Catálogo digital</x-nav-link>
                </div>
            </div>
        @endif

        @if ($user->hasRole(['administrador', 'almacenero']))
            <div>
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-widest text-slate-400">Inventario</p>
                <div class="space-y-1">
                    <x-nav-link route="inventory.index" icon="clipboard" :badge="$lowStock ?: null">Conteo físico</x-nav-link>
                    <x-nav-link route="audits.index" icon="alert">Auditorías</x-nav-link>
                    <x-nav-link route="replenishment.index" icon="refresh">Lista de reposición</x-nav-link>
                    <x-nav-link route="purchases.index" icon="truck">Compras a proveedor</x-nav-link>
                    <x-nav-link route="alerts.index" icon="alert" :badge="$pendingAlerts ?: null">Alertas</x-nav-link>
                </div>
            </div>
        @endif

        @if ($user->isAdmin())
            <div>
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-widest text-slate-400">Administración</p>
                <div class="space-y-1">
                    <x-nav-link route="products.index" icon="box">Productos</x-nav-link>
                    <x-nav-link route="categories.index" icon="tag">Categorías</x-nav-link>
                    <x-nav-link route="clients.index" icon="users">Clientes</x-nav-link>
                    <x-nav-link route="suppliers.index" icon="building">Proveedores</x-nav-link>
                    <x-nav-link route="users.index" icon="user">Usuarios y perfiles</x-nav-link>
                </div>
            </div>
        @endif
    </nav>

    <div class="border-t border-slate-100 p-3">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-slate-50">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700">
                {{ $user->initials }}
            </span>
            <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-semibold text-slate-800">{{ $user->name }}</span>
                <span class="block text-xs text-slate-500">{{ $user->role->label() }}</span>
            </span>
            <x-icon name="settings" class="h-4 w-4 text-slate-400" />
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-rose-50 hover:text-rose-700">
                <x-icon name="logout" class="h-5 w-5 text-slate-400" />
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>
