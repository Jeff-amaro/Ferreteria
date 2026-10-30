@props(['route', 'icon' => 'box', 'active' => null, 'badge' => null])

@php
    $isActive = $active ?? request()->routeIs($route.'*');
@endphp

<a href="{{ route($route) }}"
   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $isActive ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
    <x-icon :name="$icon" class="h-5 w-5 {{ $isActive ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}" />
    <span class="flex-1">{{ $slot }}</span>
    @if ($badge)
        <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-bold text-rose-700">{{ $badge }}</span>
    @endif
</a>
