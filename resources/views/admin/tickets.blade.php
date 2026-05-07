<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
                Tickets {{ $tecnico ?? null ? '(' . $tecnico->name . ')' : '' }}
            </h2>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold
       bg-slate-100 text-slate-800 hover:bg-slate-200
       dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700
       border border-slate-200 dark:border-slate-700">
                Volver
            </a>
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

                <div class="mb-4 flex flex-wrap items-end gap-4">

                    {{-- Técnico --}}
                    <div class="w-full sm:w-[260px]">

                        <div class="relative">
                            <select onchange="location = updateQuery('tecnico', this.value)"
                                class="w-full appearance-none rounded-md
                       border border-slate-200 dark:border-slate-700
                       bg-white dark:bg-slate-800
                       text-sm px-3 py-2 pr-10
                       text-slate-900 dark:text-slate-100
                       focus:outline-none focus:ring-2 focus:ring-slate-400/30">

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
                                class="w-full appearance-none rounded-md
                       border border-slate-200 dark:border-slate-700
                       bg-white dark:bg-slate-800
                       text-sm px-3 py-2 pr-10
                       text-slate-900 dark:text-slate-100
                       focus:outline-none focus:ring-2 focus:ring-slate-400/30">

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
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                            Ordenar por
                        </label>

                        <div class="relative">
                            <select onchange="location = updateQuery('order', this.value)"
                                class="w-full appearance-none rounded-md
                       border border-slate-200 dark:border-slate-700
                       bg-white dark:bg-slate-800
                       text-sm px-3 py-2 pr-10
                       text-slate-900 dark:text-slate-100
                       focus:outline-none focus:ring-2 focus:ring-slate-400/30">

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
                            <col class="w-56"> {{-- Solicitante --}}
                            <col class="w-56"> {{-- Técnico --}}
                            <col class="w-33"> {{-- Estado --}}
                            <col class="w-32"> {{-- Tiempo --}}
                            <col class="w-36"> {{-- Creado --}}
                        </colgroup>

                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 dark:text-slate-200">
                                <th class="py-2 pr-4 text-center whitespace-nowrap">#</th>
                                <th class="py-2 pr-4 text-center whitespace-nowrap">Asunto</th>
                                <th class="py-2 pr-4 text-center whitespace-nowrap">Solicitante</th>
                                <th class="py-2 pr-4 text-center whitespace-nowrap">Técnico</th>
                                <th class="py-2 pr-4 text-center whitespace-nowrap">Estado</th>
                                <th class="py-2 pr-4 text-center whitespace-nowrap">Tiempo</th>
                                <th class="py-2 pr-4 text-center whitespace-nowrap">Creado</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($tickets as $t)
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <td class="py-2 pr-4">{{ $t->id }}</td>

                                    <td class="py-2 pr-4">
                                        <a href="{{ route('tickets.show', $t) }}" class="hover:underline text-inherit">
                                            {{ $t->subject }}
                                        </a>
                                    </td>

                                    <td class="py-2 pr-4 text-center align-middle">
                                        {{ $t->creator?->name ?? '-' }}
                                    </td>

                                    <td class="py-2 pr-4 text-center align-middle">
                                        {{ $t->assignee?->name ?? '—' }}
                                    </td>

                                    @php
                                        $age = $t->ageSemaphore();
                                    @endphp

                                    <td class="py-2 pr-4 text-center align-middle">
                                        <x-status-badge :status="$t->status" />
                                    </td>

                                    <td class="py-2 pr-4 text-center align-middle">
                                        <x-time-pill :minutes="$age['minutes']" :color="$age['color']" />
                                    </td>

                                    <td class="py-2 pr-4 text-center align-middle">
                                        <span
                                            class="whitespace-nowrap">{{ $t->created_at?->format('Y-m-d H:i:s') }}</span>
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
