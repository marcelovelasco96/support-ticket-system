@props([
    // minutes as int
    'minutes' => 0,

    // 'green' | 'amber' | 'red'
    'color' => 'green',
])

@php
    $m = (int) $minutes;

    $label = $m < 60 ? $m . 'm' : floor($m / 60) . 'h ' . $m % 60 . 'm';

    $base =
        'inline-flex items-center justify-center w-[110px] rounded-full px-3 py-1 text-xs font-semibold ring-1 whitespace-nowrap';

    $class = match ($color) {
        'green'
            => 'bg-emerald-100 text-emerald-800 ring-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-200 dark:ring-emerald-800',
        'amber'
            => 'bg-amber-100 text-amber-800 ring-amber-200 dark:bg-amber-900/40 dark:text-amber-200 dark:ring-amber-800',
        default => 'bg-rose-100 text-rose-800 ring-rose-200 dark:bg-rose-900/40 dark:text-rose-200 dark:ring-rose-800',
    };
@endphp

<span @class([$base, $class])>
    {{ $label }}
</span>
