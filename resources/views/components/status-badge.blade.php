@props(['status'])

@php
    $key = preg_replace('/\s+/', '_', strtolower(trim((string) $status)));

    $map = [
        'abierto' => ['dot' => '#2563eb', 'label' => 'Abierto'],
        'en_proceso' => ['dot' => '#f59e0b', 'label' => 'En atención'],
        'resuelto' => ['dot' => '#16a34a', 'label' => 'Resuelto'],
        'cerrado' => ['dot' => '#374151', 'label' => 'Cerrado'],
    ];

    $cfg = $map[$key] ?? ['dot' => '#6b7280', 'label' => (string) $status];
@endphp

<span @class([
    'inline-flex items-center justify-center gap-2',
    'w-[140px] px-3 py-1.5 rounded-full border shrink-0',
    'text-xs font-semibold leading-none',
    // Light
    'bg-white border-slate-200 text-slate-700',
    // Dark
    'dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100',
])>
    <span class="w-3 h-3 rounded-full" style="background: {{ $cfg['dot'] }};"></span>

    <span class="inline-block w-[80px] text-center">
        {{ $cfg['label'] }}
    </span>
</span>
