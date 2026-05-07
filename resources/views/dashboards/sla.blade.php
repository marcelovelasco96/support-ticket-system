<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard SLA
            </h2>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-3 py-2 bg-gray-200 rounded text-xs font-semibold">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h3 class="font-semibold text-gray-800 mb-4">Resumen</h3>

                {{-- Indicador general SLA (gerencial) --}}
                <div class="mb-6">

                    @php
                        $bg = match ($slaStatus ?? 'warning') {
                            'ok' => 'bg-green-50 border-green-200',
                            'warning' => 'bg-yellow-50 border-yellow-200',
                            'danger' => 'bg-red-50 border-red-200',
                            default => 'bg-gray-50 border-gray-200',
                        };

                        $text = match ($slaStatus ?? 'warning') {
                            'ok' => 'text-green-800',
                            'warning' => 'text-yellow-800',
                            'danger' => 'text-red-800',
                            default => 'text-gray-800',
                        };

                        $dot = match ($slaStatus ?? 'warning') {
                            'ok' => 'bg-green-500',
                            'warning' => 'bg-yellow-500',
                            'danger' => 'bg-red-500',
                            default => 'bg-gray-500',
                        };
                    @endphp

                    @if (($slaStatus ?? null) === 'warning')
                        <div class="mb-4 p-3 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 text-sm">
                            ⚠️ Atención: el SLA global está en observación ({{ $pctOnTime ?? 0 }}% de cumplimiento).
                        </div>
                    @endif

                    @if (($slaStatus ?? null) === 'danger')
                        <div class="mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-800 text-sm">
                            ⛔ Alerta: el SLA global está en riesgo ({{ $pctOnTime ?? 0 }}% de cumplimiento).
                        </div>
                    @endif

                    <div class="p-5 rounded-lg border {{ $bg }} flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-block w-3 h-3 rounded-full {{ $dot }}"></span>
                            <div>
                                <div class="text-xs uppercase tracking-wide {{ $text }}">Estado general SLA
                                </div>
                                <div class="text-lg font-bold {{ $text }}">
                                    {{ $slaText }}
                                </div>
                            </div>
                        </div>

                        <div class="text-sm {{ $text }}">
                            Cumplimiento: <span class="font-semibold">{{ $pctOnTime ?? 0 }}%</span>
                        </div>
                    </div>
                </div>

                @php
                    $slaBoxClass = match ($slaStatus ?? 'warning') {
                        'ok' => 'bg-green-50 border-green-200',
                        'warning' => 'bg-yellow-50 border-yellow-200',
                        'danger' => 'bg-red-50 border-red-200',
                        default => 'bg-gray-50 border-gray-200',
                    };

                    $slaTextClass = match ($slaStatus ?? 'warning') {
                        'ok' => 'text-green-700',
                        'warning' => 'text-yellow-700',
                        'danger' => 'text-red-700',
                        default => 'text-gray-700',
                    };
                @endphp

                <div class="grid grid-cols-2 gap-4">

                    @php
                        // Semáforo por % A tiempo
                        $slaTone = 'green'; // default

                        if ($pctOnTime < 60) {
                            $slaTone = 'red';
                        } elseif ($pctOnTime < 80) {
                            $slaTone = 'yellow';
                        }

                        $slaCardClasses = match ($slaTone) {
                            'green' => 'border border-green-200 bg-green-50',
                            'yellow' => 'border border-yellow-200 bg-yellow-50',
                            'red' => 'border border-red-200 bg-red-50',
                            default => 'border',
                        };

                        $slaTextClasses = match ($slaTone) {
                            'green' => 'text-green-700',
                            'yellow' => 'text-yellow-700',
                            'red' => 'text-red-700',
                            default => 'text-gray-500',
                        };
                    @endphp

                    <div class="p-4 rounded border">
                        <div class="text-gray-500">Total tickets</div>
                        <div class="text-2xl font-bold">{{ $total }}</div>
                    </div>

                    <div class="p-4 rounded border">
                        <div class="text-gray-500">Abiertos</div>
                        <div class="text-2xl font-bold">{{ $openCount }}</div>
                    </div>

                    <div class="p-4 rounded border">
                        <div class="text-gray-500">En proceso</div>
                        <div class="text-2xl font-bold">{{ $inProcessCount }}</div>
                    </div>

                    <div class="p-4 rounded border">
                        <div class="text-gray-500">Resueltos</div>
                        <div class="text-2xl font-bold">{{ $resolvedCount }}</div>
                    </div>

                    <div class="p-4 rounded border">
                        <div class="text-gray-500">Cerrados</div>
                        <div class="text-2xl font-bold">{{ $closedCount }}</div>
                    </div>

                    <div class="p-4 rounded border {{ $slaBoxClass }}">
                        <div class="text-gray-500">A tiempo</div>
                        <div class="text-2xl font-bold">{{ $onTime }}</div>
                        <div class="font-semibold {{ $slaTextClass }}">{{ $pctOnTime }}%</div>
                    </div>

                    <div class="p-4 rounded border {{ $slaBoxClass }}">
                        <div class="text-gray-500">Atrasados</div>
                        <div class="text-2xl font-bold">{{ $late }}</div>
                        <div class="font-semibold {{ $slaTextClass }}">{{ $pctLate }}%</div>
                    </div>

                    <div class="p-4 rounded border">
                        <div class="text-gray-500">Reglas SLA</div>
                        <div class="text-xs text-gray-700 mt-1">
                            Crítica: 2h · Alta: 4h · Media: 24h · Baja: 72h
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Promedio de resolución (horas) por prioridad</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 pr-4">Prioridad</th>
                                <th class="py-2 pr-4">Promedio (h)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $labels = [
                                    'critica' => 'Crítica (2h)',
                                    'alta' => 'Alta (4h)',
                                    'media' => 'Media (24h)',
                                    'baja' => 'Baja (72h)',
                                ];
                            @endphp

                            @foreach (['critica', 'alta', 'media', 'baja'] as $p)
                                <tr class="border-b">
                                    <td class="py-2 pr-4">{{ $labels[$p] }}</td>
                                    <td class="py-2 pr-4">
                                        @if (is_null($avgResolutionHoursByPriority[$p]))
                                            <span class="text-gray-400">—</span>
                                        @else
                                            {{ $avgResolutionHoursByPriority[$p] }} h
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-xs text-gray-500 mt-3">
                    Nota: el promedio se calcula desde “tomado” (taken_at) hasta “resuelto/cerrado”.
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Tickets asignados por técnico</h3>

                @if (($topTech ?? collect())->count() === 0)
                    <div class="text-sm text-gray-500">Aún no hay tickets asignados a técnicos.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2 pr-4">Técnico</th>
                                    <th class="py-2 pr-4">Total asignados</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topTech as $row)
                                    <tr class="border-b">
                                        <td class="py-2 pr-4">{{ $row['tech_name'] }}</td>
                                        <td class="py-2 pr-4 font-semibold">{{ $row['total'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Productividad: Resueltos / Cerrados por técnico</h3>

                @if (($topDoneTech ?? collect())->count() === 0)
                    <div class="text-sm text-gray-500">Aún no hay tickets resueltos o cerrados por técnicos.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2 pr-4">Técnico</th>
                                    <th class="py-2 pr-4">Resueltos/Cerrados</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topDoneTech as $row)
                                    <tr class="border-b">
                                        <td class="py-2 pr-4">{{ $row['tech_name'] }}</td>
                                        <td class="py-2 pr-4 font-semibold">{{ $row['total_done'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">SLA por técnico (A tiempo vs Atrasado)</h3>

                @if (($slaByTech ?? collect())->count() === 0)
                    <div class="text-sm text-gray-500">Aún no hay tickets asignados para calcular SLA por técnico.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2 pr-4">Técnico</th>
                                    <th class="py-2 pr-4">Total</th>
                                    <th class="py-2 pr-4">A tiempo</th>
                                    <th class="py-2 pr-4">Atrasado</th>
                                    <th class="py-2 pr-4">% A tiempo</th>
                                    <th class="py-2 pr-4">Progreso</th>
                                    <th class="py-2 pr-4">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($slaByTech as $row)
                                    @php
                                        $techPct = (float) ($row->pct_on_time ?? 0);

                                        if ($techPct >= 80) {
                                            $techStatus = 'ok';
                                            $techBg = 'bg-green-50 border-green-300';
                                            $techDot = 'bg-green-500';
                                            $techText = 'text-green-700';
                                            $techLabel = 'Saludable';
                                        } elseif ($techPct >= 60) {
                                            $techStatus = 'warning';
                                            $techBg = 'bg-yellow-50 border-yellow-300';
                                            $techDot = 'bg-yellow-500';
                                            $techText = 'text-yellow-700';
                                            $techLabel = 'Observación';
                                        } else {
                                            $techStatus = 'danger';
                                            $techBg = 'bg-red-50 border-red-300';
                                            $techDot = 'bg-red-500';
                                            $techText = 'text-red-700';
                                            $techLabel = 'Crítico';
                                        }
                                    @endphp

                                    <tr class="border-b">
                                        <td class="py-2 pr-4">
                                            <a href="{{ url('/admin/tickets?tecnico=' . $row['tech_id']) }}"
                                                class="text-blue-700 hover:underline font-semibold">
                                                {{ $row['tech_name'] }}
                                            </a>
                                        </td>
                                        <td class="py-2 pr-4 font-semibold">{{ $row['total'] }}</td>
                                        <td class="py-2 pr-4">{{ $row['on_time'] }}</td>
                                        <td class="py-2 pr-4">{{ $row['late'] }}</td>

                                        @php
                                            $pct = (float) $row['pct_on_time'];
                                            $pctClass =
                                                $pct >= 80
                                                    ? 'text-green-700 bg-green-50 border-green-200'
                                                    : ($pct >= 50
                                                        ? 'text-yellow-700 bg-yellow-50 border-yellow-200'
                                                        : 'text-red-700 bg-red-50 border-red-200');
                                        @endphp

                                        @php
                                            $pct = (int) ($row['pct_on_time'] ?? 0);

                                            $pctStyle =
                                                $pct >= 80
                                                    ? 'border:1px solid #86efac; background:#ecfdf5; color:#166534;' // verde
                                                    : ($pct >= 50
                                                        ? 'border:1px solid #fde68a; background:#fffbeb; color:#92400e;' // amarillo
                                                        : 'border:1px solid #fca5a5; background:#fef2f2; color:#991b1b;'); // rojo
                                        @endphp

                                        <td class="py-2 pr-4">
                                            <x-percent-pill :value="$pct" />
                                        </td>

                                        <td class="py-2 pr-4">
                                            @php
                                                $pct = (int) ($row['pct_on_time'] ?? 0);
                                                if ($pct < 0) {
                                                    $pct = 0;
                                                }
                                                if ($pct > 100) {
                                                    $pct = 100;
                                                }

                                                $barClass =
                                                    $pct >= 80
                                                        ? 'bg-green-500'
                                                        : ($pct >= 50
                                                            ? 'bg-yellow-500'
                                                            : 'bg-red-500');
                                            @endphp

                                            <div class="w-48 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-2 {{ $barClass }}"
                                                    style="width: {{ $pct }}%"></div>
                                            </div>

                                        </td>

                                        <td class="py-2 pr-4">
                                            <div
                                                class="inline-flex items-center gap-2 px-2 py-1 rounded border {{ $techBg }}">
                                                <span
                                                    class="inline-block w-2.5 h-2.5 rounded-full {{ $techDot }}"></span>
                                                <span class="text-xs font-semibold {{ $techText }}">
                                                    {{ $techLabel }}
                                                </span>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-xs text-gray-500 mt-3">
                        Nota: el cálculo usa el mismo criterio que tu SLA: abierto (creado→ahora) y en
                        proceso/resuelto/cerrado (tomado→resuelto/cerrado/ahora).
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
