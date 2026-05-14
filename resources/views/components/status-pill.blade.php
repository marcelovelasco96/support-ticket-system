@props([
    'status' => '',
    'width' => 160,
    'height' => 30,
])

@php
    $st = $status;

    if ($st === 'abierto') {
        $dot = '#2563eb';
        $bg = '#eff6ff';
        $bd = '#93c5fd';
        $tx = '#1d4ed8';
        $label = 'Abierto';
    } elseif ($st === 'en_proceso') {
        $dot = '#f59e0b';
        $bg = '#fff7ed';
        $bd = '#fdba74';
        $tx = '#9a3412';
        $label = 'En proceso';
    } elseif ($st === 'resuelto') {
        $dot = '#16a34a';
        $bg = '#f0fdf4';
        $bd = '#86efac';
        $tx = '#166534';
        $label = 'Resuelto';
    } elseif ($st === 'cerrado') {
        $dot = '#111827';
        $bg = '#f3f4f6';
        $bd = '#d1d5db';
        $tx = '#111827';
        $label = 'Cerrado';
    } else {
        $dot = '#6b7280';
        $bg = '#f9fafb';
        $bd = '#e5e7eb';
        $tx = '#374151';
        $label = $st;
    }
@endphp

<div style="display:flex; align-items:center;">
    <div
        style="
        width:{{ (int) $width }}px;
        height:{{ (int) $height }}px;
        display:flex;
        align-items:center;
        justify-content:center;
        gap:10px;
        font-size:12px;
        font-weight:600;
        border-radius:999px;
        box-sizing:border-box;
        border:1px solid {{ $bd }};
        background: {{ $bg }};
        color: {{ $tx }};
        white-space:nowrap;
    ">
        <span
            style="width:10px;height:10px;border-radius:999px;background:{{ $dot }};display:inline-block;"></span>
        <span>{{ $label }}</span>
    </div>
</div>
