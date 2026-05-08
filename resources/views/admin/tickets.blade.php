<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl leading-tight text-slate-900 dark:text-slate-100">
                    Bandeja General de Tickets
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Seguimiento operativo de tickets
                    {{ $tecnico ?? null ? 'asignados a ' . $tecnico->name : 'registrados' }}.
                </p>
            </div>

        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-slate-900 shadow-sm sm:rounded-lg p-6
            text-gray-900 dark:text-slate-100
            dark:border dark:border-slate-800">

                @php
                    $tecnicos = \App\Models\User::role('tecnico')->orderBy('name')->get();

                    $statusOptions = [
                        '' => 'Todos',
                        'abierto' => 'Abierto',
                        'en_proceso' => 'En atención',
                        'finalizados' => 'Finalizados',
                        'resuelto' => 'Resuelto',
                        'cerrado' => 'Cerrado',
                    ];

                    $orderOptions = [
                        'recientes' => 'Más recientes',
                        'antiguos' => 'Más antiguos',
                    ];
                @endphp

                <div class="mb-5 flex flex-wrap items-center gap-2">

                    {{-- Técnico --}}
                    <div class="w-full sm:w-[260px]">

                        <div class="relative">
                            <select onchange="location = updateQuery('tecnico', this.value)"
                                class="w-full appearance-none rounded-lg
                                        border border-slate-200 dark:border-slate-700
                                        bg-white dark:bg-slate-800
                                        text-sm px-3 py-2 pr-10
                                        text-slate-900 dark:text-slate-100
                                        shadow-sm
                                        focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <option value="">Técnico: Todos</option>

                                @foreach ($tecnicos as $tec)
                                    <option value="{{ $tec->id }}" @selected(request('tecnico') == $tec->id)>
                                        {{ $tec->name }}
                                    </option>
                                @endforeach
                            </select>

                            <span
                                class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-500 dark:text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div class="w-full sm:w-[220px]">

                        <div class="relative">
                            <select onchange="location = updateQuery('status', this.value)"
                                class="w-full appearance-none rounded-lg
                                        border border-slate-200 dark:border-slate-700
                                        bg-white dark:bg-slate-800
                                        text-sm px-3 py-2 pr-10
                                        text-slate-900 dark:text-slate-100
                                        shadow-sm
                                        focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                @foreach ($statusOptions as $val => $label)
                                    @php
                                        // Para que el default diga "Todos los estados" (no solo "Todos")
                                        $labelFinal = $val === '' ? 'Estado: Todos' : $label;
                                    @endphp

                                    <option value="{{ $val }}" @selected(request('status', '') === $val)>
                                        {{ $labelFinal }}
                                    </option>
                                @endforeach
                            </select>

                            <span
                                class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-500 dark:text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    {{-- Ordenar --}}
                    <div class="w-full sm:w-[220px]">

                        <div class="relative">
                            <select onchange="location = updateQuery('order', this.value)"
                                class="w-full appearance-none rounded-lg
                                        border border-slate-200 dark:border-slate-700
                                        bg-white dark:bg-slate-800
                                        text-sm px-3 py-2 pr-10
                                        text-slate-900 dark:text-slate-100
                                        shadow-sm
                                        focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                @foreach ($orderOptions as $val => $label)
                                    <option value="{{ $val }}" @selected(request('order', 'recientes') === $val)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            <span
                                class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-500 dark:text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm table-fixed">

                        <colgroup>
                            <col class="w-16"> {{-- # --}}
                            <col> {{-- Asunto --}}
                            <col class="w-32"> {{-- Área --}}
                            <col class="w-44"> {{-- Solicitante --}}
                            <col class="w-44"> {{-- Técnico --}}
                            <col class="w-44"> {{-- Estado --}}
                            <col class="w-28"> {{-- Tiempo --}}
                            <col class="w-36"> {{-- Creado --}}
                        </colgroup>

                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr
                                class="border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-300">
                                <th class="py-3 pr-4 text-center whitespace-nowrap">#</th>
                                <th class="py-3 pr-4 text-center whitespace-nowrap">Asunto</th>
                                <th class="py-3 pr-4 text-center whitespace-nowrap">Área</th>
                                <th class="py-3 pr-4 text-center whitespace-nowrap">Solicitante</th>
                                <th class="py-3 pr-4 text-center whitespace-nowrap">Técnico</th>
                                <th class="py-3 pr-4 text-center whitespace-nowrap">Estado</th>
                                <th class="py-3 pr-4 text-center whitespace-nowrap">Tiempo</th>
                                <th class="py-3 pr-4 text-center whitespace-nowrap">Creado</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($tickets as $t)
                                <tr
                                    class="border-b border-slate-100 dark:border-slate-800
                                            hover:bg-slate-50/80 dark:hover:bg-slate-800/60
                                            transition-colors duration-150">
                                    <td class="py-3 pr-4">{{ $t->id }}</td>

                                    <td class="py-3 pr-4">
                                        <a href="{{ route('tickets.show', $t) }}"
                                            class="font-medium leading-snug max-w-none text-slate-900 dark:text-slate-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $t->subject }}
                                        </a>
                                    </td>

                                    <td class="py-3 pr-4 text-center align-middle text-slate-700 dark:text-slate-300">
                                        {{ $t->creator?->area?->short_name ?? ($t->creator?->area?->name ?? '—') }}
                                    </td>

                                    <td class="py-3 pr-4 text-center align-middle text-slate-700 dark:text-slate-300">
                                        {{ $t->creator?->name ?? '-' }}
                                    </td>

                                    <td class="py-3 pr-4 text-center align-middle text-slate-700 dark:text-slate-300">
                                        {{ $t->assignee?->name ?? '—' }}
                                    </td>

                                    @php
                                        $age = $t->ageSemaphore();
                                    @endphp

                                    <td class="py-3 pr-4 text-center align-middle">
                                        <x-status-badge :status="$t->status" />
                                    </td>

                                    <td class="py-3 pr-4 text-center align-middle">
                                        <x-time-pill :minutes="$age['minutes']" :color="$age['color']" />
                                    </td>

                                    <td
                                        class="py-3 pr-4 text-center align-middle text-xs text-slate-500 dark:text-slate-400">
                                        <span class="whitespace-nowrap">
                                            @if ($t->created_at?->isToday())
                                                Hoy · {{ $t->created_at->format('H:i') }}
                                            @elseif ($t->created_at?->isYesterday())
                                                Ayer · {{ $t->created_at->format('H:i') }}
                                            @else
                                                {{ $t->created_at?->translatedFormat('d M') }} ·
                                                {{ $t->created_at?->format('H:i') }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500">
                                        No hay tickets para mostrar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $tickets->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function updateQuery(key, value) {
        const url = new URL(window.location);

        if (value === '') {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, value);
        }

        return url.toString();
    }
</script>
