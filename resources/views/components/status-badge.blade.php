@props(['color' => 'bg-slate-100 text-slate-700'])

<span {{ $attributes->merge(['class' => 'badge '.$color]) }}>{{ $slot }}</span>
