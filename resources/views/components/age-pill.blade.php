@props([
    'color' => 'green', // green | amber | red
    'phase' => 'COLA', // COLA | PROC
    'minutes' => 0,
])

@php
    $m = (int) $minutes;

    $timeLabel = $m < 60 ? $m . 'm' : floor($m / 60) . 'h ' . $m % 60 . 'm';

    $base =
        'inline-flex items-center justify-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ring-1 whitespace-nowrap';

    $phaseLabel = match ($phase) {
        'COLA' => 'EN COLA',
        'PROC' => 'EN ATENCIÓN',
        'FIN' => 'FINALIZADO',
        default => $phase,
    };
@endphp

<span @class([
    $base,

    // GREEN
    'bg-emerald-100 text-emerald-800 ring-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-200 dark:ring-emerald-800' =>
        $color === 'green',

    // AMBER
    'bg-amber-100 text-amber-800 ring-amber-200 dark:bg-amber-900/40 dark:text-amber-200 dark:ring-amber-800' =>
        $color === 'amber',

    // RED
    'bg-rose-100 text-rose-800 ring-rose-200 dark:bg-rose-900/40 dark:text-rose-200 dark:ring-rose-800' =>
        $color === 'red',
])>
    <span class="inline-flex items-center gap-1">
        <span class="h-2 w-2 rounded-full" @class([
            'bg-emerald-500' => $color === 'green',
            'bg-amber-500' => $color === 'amber',
            'bg-rose-500' => $color === 'red',
        ])></span>
        <span>{{ $phaseLabel }}</span>
    </span>

    <span class="opacity-90">{{ $timeLabel }}</span>
</span>
