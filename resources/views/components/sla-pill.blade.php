@props([
    'ticket' => null,
    'priority' => null,
    'width' => 160,
    'height' => 30,
])

@php
    $rawPriority = (string) ($priority ?? ($ticket->priority ?? 'media'));

    // Normaliza: minúsculas + quita caracteres raros/tildes (Cr�tica, Crítica, CRITICA, etc.)
    $p = mb_strtolower(trim($rawPriority), 'UTF-8');

    // reemplazos manuales útiles (por si viene con símbolos raros)
    $p = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'u', 'n'], $p);

    $p = preg_replace('/[^a-z]/', '', $p);

    // si vino como "cr�tica" (caracter raro), esto lo vuelve "crtica", así que forzamos regla por "crt"
    $limitHours = match (true) {
        str_contains($p, 'crit') || str_contains($p, 'crt') => 2,
        str_contains($p, 'alta') => 4,
        str_contains($p, 'baja') => 72,
        default => 24,
    };
@endphp


@php
    if (!$ticket) {
        $h = 0;
        $m = 0;
        $bg = '#f9fafb';
        $border = '#e5e7eb';
        $text = '#374151';
    } else {
        $t = $ticket;

        $createdAt = $t->created_at ? \Carbon\Carbon::parse($t->created_at) : null;
        $takenAt = $t->taken_at ? \Carbon\Carbon::parse($t->taken_at) : null;
        $resolvedAt = $t->resolved_at ? \Carbon\Carbon::parse($t->resolved_at) : null;
        $closedAt = $t->closed_at ? \Carbon\Carbon::parse($t->closed_at) : null;

        // Regla:
        // - Si está ABIERTO: SLA = desde created_at hasta ahora
        // - Si está EN PROCESO: SLA = desde taken_at (o created_at si faltara) hasta ahora
        // - Si está RESUELTO/CERRADO: SLA = desde taken_at (o created_at) hasta resolved_at/closed_at
        if ($t->status === 'abierto') {
            $from = $createdAt;
            $to = \Carbon\Carbon::now();
        } else {
            $from = $takenAt ?: $createdAt;
            $to = $closedAt ?: ($resolvedAt ?: \Carbon\Carbon::now());
        }

        $minutes = $from && $to ? $from->diffInMinutes($to) : 0;

        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        $isLate = $minutes > (int) $limitHours * 60;

        $bg = $isLate ? '#fef2f2' : '#f0fdf4';
        $border = $isLate ? '#fca5a5' : '#86efac';
        $text = $isLate ? '#991b1b' : '#065f46';
    }
@endphp

<div
    style="
    width:{{ (int) $width }}px;
    height:{{ (int) $height }}px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    font-weight:600;
    border-radius:999px;
    box-sizing:border-box;
    border:1px solid {{ $border }};
    background:{{ $bg }};
    color:{{ $text }};
    white-space:nowrap;
">
    SLA: {{ $h }}h {{ $m }}m / {{ (int) $limitHours }}h
</div>
