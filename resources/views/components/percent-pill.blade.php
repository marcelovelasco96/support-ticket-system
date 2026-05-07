@props([
    'value' => 0, // número 0..100
    'width' => 72,
    'height' => 26,
])

@php
    $v = (int) $value;

    // Colores por rangos (ajustables)
    if ($v >= 80) {
        $bg = '#ecfdf5';
        $border = '#86efac';
        $text = '#065f46'; // verde
    } elseif ($v >= 50) {
        $bg = '#fffbeb';
        $border = '#fcd34d';
        $text = '#92400e'; // ámbar
    } else {
        $bg = '#fff1f2';
        $border = '#fca5a5';
        $text = '#991b1b'; // rojo
    }
@endphp

<span
    style="
    width:{{ (int) $width }}px;
    height:{{ (int) $height }}px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    font-weight:700;
    letter-spacing:0.2px;
    border-radius:9999px;
    box-sizing:border-box;
    white-space:nowrap;
    border:1px solid {{ $border }};
    background:{{ $bg }};
    color:{{ $text }};
">
    {{ $v }}%
</span>
