<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl leading-tight text-slate-900 dark:text-slate-100">
                    Panel de Administración
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                    Vista general del sistema: tickets, carga de técnicos y accesos rápidos.
                </p>
            </div>

        </div>
    </x-slot>

    @php
        // KPIs
        $total = \App\Models\Ticket::count();
        $abiertos = \App\Models\Ticket::where('status', 'abierto')->count();
        $enAtencion = \App\Models\Ticket::where('status', 'en_proceso')->count();
        $resueltos = \App\Models\Ticket::where('status', 'resuelto')->count();
        $cerrados = \App\Models\Ticket::where('status', 'cerrado')->count();

        // Últimos tickets
        $latest = \App\Models\Ticket::with(['creator', 'assignee'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Carga por técnico (solo técnicos)
        // Regla nueva:
        // - en atención = en_proceso
        // - asignados = todos los tickets asignados al técnico
        //   (en_proceso + resuelto + cerrado)
        // - atendidos = resuelto + cerrado

        $tecnicos = \App\Models\User::role('tecnico')
            ->orderBy('name')
            ->withCount([
                // En atención actual
                'assignedTickets as in_process_count' => function ($q) {
                    $q->where('status', 'en_proceso');
                },

                // Total asignados históricos
                'assignedTickets as assigned_total_count',

                // Atendidos/resueltos
                'assignedTickets as solved_count' => function ($q) {
                    $q->whereIn('status', ['resuelto', 'cerrado']);
                },
            ])
            ->get()
            ->map(function ($u) {
                $inProcess = (int) $u->in_process_count;
                $assignedTotal = (int) $u->assigned_total_count;
                $solved = (int) $u->solved_count;

                $u->assigned_count = $inProcess;
                $u->resolved_count = $assignedTotal;
                $u->solved_only = $solved;

                $u->resolved_pct = $assignedTotal > 0 ? (int) round(($solved / $assignedTotal) * 100) : 0;

                // ===== Promedio de atención =====
                $avgMinutes = \App\Models\Ticket::query()
                    ->where('assigned_to', $u->id)
                    ->whereNotNull('taken_at')
                    ->whereNotNull('resolved_at')
                    ->whereIn('status', ['resuelto', 'cerrado'])
                    ->get()
                    ->avg(function ($t) {
                        return $t->taken_at->diffInMinutes($t->resolved_at);
                    });

                if ($avgMinutes) {
                    $hours = floor($avgMinutes / 60);
                    $minutes = round($avgMinutes % 60);

                    $u->avg_attention = ($hours ? $hours . 'h ' : '') . $minutes . 'm';
                } else {
                    $u->avg_attention = '—';
                }

                // ===== Opción A: Área actual = área del ticket MÁS ANTIGUO en atención del técnico =====
                $oldestInProgress = \App\Models\Ticket::with('creator.area')
                    ->where('assigned_to', $u->id)
                    ->where('status', 'en_proceso')
                    ->orderByRaw('CASE WHEN taken_at IS NULL THEN created_at ELSE taken_at END ASC')
                    ->first();

                $u->current_area =
                    $oldestInProgress?->creator?->area?->short_name ??
                    ($oldestInProgress?->creator?->area?->name ?? null);

                // ===== Opción B: En atención por áreas (conteo) =====
                $areas = \App\Models\Ticket::query()
                    ->join('users as cu', 'cu.id', '=', 'tickets.created_by')
                    ->leftJoin('areas as a', 'a.id', '=', 'cu.area_id')
                    ->where('tickets.assigned_to', $u->id)
                    ->where('tickets.status', 'en_proceso')
                    ->selectRaw("COALESCE(a.short_name, a.name, '—') as area_label, COUNT(*) as cnt")
                    ->groupByRaw("COALESCE(a.short_name, a.name, '—')")
                    ->orderByDesc('cnt')
                    ->get();

                $u->areas_breakdown = $areas->isEmpty()
                    ? null
                    : $areas->map(fn($r) => "{$r->area_label} ({$r->cnt})")->implode(', ');

                return $u;
            });
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- KPI Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Total --}}
                <a href="{{ route('admin.tickets') }}"
                    class="group rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800
                           shadow-sm hover:shadow transition">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total tickets</div>
                    <div class="mt-2 flex items-end justify-between">
                        <div class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $total }}</div>
                        <div
                            class="text-xs text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200 transition">
                            Ver todo →
                        </div>
                    </div>
                </a>

                {{-- Abierto --}}
                <a href="{{ route('admin.tickets', ['status' => 'abierto']) }}"
                    class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800
                           shadow-sm hover:shadow transition">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Abiertos</div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $abiertos }}</div>
                        <span
                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-200">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            En cola
                        </span>
                    </div>
                </a>

                {{-- En atención --}}
                <a href="{{ route('admin.tickets', ['status' => 'en_proceso']) }}"
                    class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800
                           shadow-sm hover:shadow transition">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">En atención</div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $enAtencion }}</div>
                        <span
                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-200">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Activos
                        </span>
                    </div>
                </a>

                {{-- Resuelto --}}
                <a href="{{ route('admin.tickets', ['status' => 'resuelto']) }}"
                    class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800
                           shadow-sm hover:shadow transition">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Resueltos</div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $resueltos }}</div>
                        <span
                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-200">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-600"></span>
                            OK
                        </span>
                    </div>
                </a>

                {{-- Cerrado --}}
                <a href="{{ route('admin.tickets', ['status' => 'cerrado']) }}"
                    class="rounded-2xl p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800
                           shadow-sm hover:shadow transition">
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Cerrados</div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $cerrados }}</div>
                        <span
                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-200">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-600"></span>
                            Final
                        </span>
                    </div>
                </a>
            </div>

            {{-- Main grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Últimos tickets --}}
                <div
                    class="lg:col-span-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="p-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Últimos tickets</h3>
                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">Actividad reciente del sistema.
                            </p>
                        </div>

                        <a href="{{ route('admin.tickets') }}"
                            class="text-sm font-semibold text-slate-700 hover:underline dark:text-slate-200">
                            Ver todos
                        </a>
                    </div>

                    <div class="px-5 pb-5 overflow-x-auto">
                        <table class="w-full text-sm table-fixed">
                            <colgroup>
                                <col class="w-16">
                                <col>
                                <col class="w-44">
                                <col class="w-44">
                                <col class="w-44">
                            </colgroup>

                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300">
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">#</th>
                                    <th class="py-2 pr-4 text-left whitespace-nowrap">Asunto</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Estado</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Tiempo</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Creado</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($latest as $t)
                                    @php $age = $t->ageSemaphore(); @endphp

                                    <tr
                                        class="border-b border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                        <td class="py-2 pr-4 text-center align-middle">{{ $t->id }}</td>

                                        <td class="py-2 pr-4 align-middle">
                                            <a href="{{ route('tickets.show', $t) }}"
                                                class="hover:underline text-slate-900 dark:text-slate-100 block truncate"
                                                title="{{ $t->subject }}">
                                                {{ $t->subject }}
                                            </a>
                                            <div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400 truncate">
                                                {{ $t->creator?->name ?? '—' }}
                                                @if ($t->assignee?->name)
                                                    • Técnico: {{ $t->assignee->name }}
                                                @endif
                                            </div>
                                        </td>

                                        <td class="py-2 pr-4 text-center align-middle">
                                            <x-status-badge :status="$t->status" />
                                        </td>

                                        <td class="py-2 pr-4 text-center align-middle">
                                            <x-time-pill :minutes="$age['minutes']" :color="$age['color']" />
                                        </td>

                                        <td
                                            class="py-2 pr-4 text-center align-middle whitespace-nowrap text-slate-600 dark:text-slate-300">
                                            {{ $t->created_at?->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-500 dark:text-slate-400">
                                            No hay tickets para mostrar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Carga por técnico --}}
                <div
                    class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="p-5">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Carga por técnico</h3>
                        <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                            En atención, asignados históricos y atendidos por técnico.
                        </p>
                    </div>

                    <div class="px-5 pb-5 space-y-3">
                        @foreach ($tecnicos as $tec)
                            <a href="{{ route('admin.tickets', ['tecnico' => $tec->id]) }}"
                                @class([
                                    'block rounded-lg p-4 transition',
                                    'bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/60',
                                    'border border-amber-300 dark:border-amber-600 hover:border-amber-400 dark:hover:border-amber-500' =>
                                        $tec->assigned_count >= 3,
                                    'border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700' =>
                                        $tec->assigned_count < 3,
                                ])>

                                <div class="flex items-start justify-between gap-3">

                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 dark:text-slate-100 truncate">
                                            {{ $tec->name }}
                                        </div>

                                        <div
                                            class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-600 dark:text-slate-300">

                                            <span>
                                                <span
                                                    class="font-semibold text-slate-900 dark:text-slate-100">{{ $tec->assigned_count }}</span>
                                                en atención
                                            </span>

                                            <span class="text-slate-400 dark:text-slate-500">•</span>

                                            <span>
                                                <span
                                                    class="font-semibold text-slate-900 dark:text-slate-100">{{ $tec->solved_only }}</span>
                                                atendidos
                                            </span>

                                        </div>

                                        @if (!empty($tec->current_area))
                                            <div class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                                                <span class="font-semibold text-slate-900 dark:text-slate-100">Área
                                                    actual:</span>
                                                {{ $tec->current_area }}
                                            </div>
                                        @endif

                                        @if (!empty($tec->areas_breakdown))
                                            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                <span class="font-semibold">En atención por áreas:</span>
                                                {{ $tec->areas_breakdown }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <div class="text-xs text-slate-500 dark:text-slate-300">Progreso</div>
                                        <div class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                            {{ $tec->resolved_pct }}%
                                        </div>
                                    </div>

                                </div>

                                <div
                                    class="mt-3 h-2 w-full rounded-full bg-slate-200 dark:bg-slate-800 overflow-hidden">
                                    <div class="h-full rounded-full bg-emerald-600"
                                        style="width: {{ $tec->resolved_pct }}%"></div>
                                </div>

                                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Tiempo promedio de resolución:
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">
                                        {{ $tec->avg_attention }}
                                    </span>
                                </div>

                            </a>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
