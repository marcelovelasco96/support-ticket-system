<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Panel del Usuario
            </h2>

            <a href="{{ route('tickets.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-xs font-semibold uppercase tracking-widest
           bg-[#00528e] text-white hover:bg-[#003f6c] shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Crear Ticket
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">


                    {{-- Total --}}
                    <a href="{{ route('tickets.index') }}"
                        class="block p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700
               hover:border-gray-300 dark:hover:border-gray-500 transition
               border-l-4 border-l-gray-300 dark:border-l-gray-600">

                        <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
                        <div class="mt-1 text-2xl font-semibold">{{ $stats['total'] }}</div>
                    </a>

                    {{-- En cola --}}
                    <a href="{{ route('tickets.index', ['status' => 'abierto']) }}"
                        class="block p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700
               hover:border-gray-300 dark:hover:border-gray-500 transition
               border-l-4 border-l-blue-500 dark:border-l-blue-400">

                        <div class="text-xs text-gray-500 dark:text-gray-400">Abierto</div>
                        <div class="mt-1 text-2xl font-semibold">{{ $stats['abierto'] }}</div>
                    </a>

                    {{-- En atención --}}
                    <a href="{{ route('tickets.index', ['status' => 'en_proceso']) }}"
                        class="block p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700
               hover:border-gray-300 dark:hover:border-gray-500 transition
               border-l-4 border-l-amber-500 dark:border-l-amber-400">

                        <div class="text-xs text-gray-500 dark:text-gray-400">En atención</div>
                        <div class="mt-1 text-2xl font-semibold">{{ $stats['en_proceso'] }}</div>
                    </a>

                    {{-- Finalizados --}}
                    <a href="{{ route('tickets.index', ['status' => 'finalizados']) }}"
                        class="block p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700
               hover:border-gray-300 dark:hover:border-gray-500 transition
               border-l-4 border-l-green-500 dark:border-l-green-400">

                        <div class="text-xs text-gray-500 dark:text-gray-400">Resuelto / Cerrado</div>
                        <div class="mt-1 text-2xl font-semibold">
                            {{ ($stats['resuelto'] ?? 0) + ($stats['cerrado'] ?? 0) }}
                        </div>
                    </a>

                </div>


                @php
                    $tieneAbiertos = ($stats['abierto'] ?? 0) > 0;
                    $tieneEnProceso = ($stats['en_proceso'] ?? 0) > 0;
                @endphp

                @if ($tieneAbiertos || $tieneEnProceso)
                    <div
                        class="mt-6 p-4 rounded-lg border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30 text-sm text-amber-800 dark:text-amber-200">
                        Tienes
                        <strong>{{ $stats['abierto'] ?? 0 }}</strong> ticket(s) en cola y
                        <strong>{{ $stats['en_proceso'] ?? 0 }}</strong> en atención.
                        Revisa el detalle para ver el estado y las actualizaciones del técnico.
                    </div>
                @else
                    <div
                        class="mt-6 p-4 rounded-lg border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-900/30 text-sm text-green-800 dark:text-green-200">
                        🎉 No tienes tickets pendientes en este momento.
                    </div>
                @endif

                <div
                    class="mt-4 border-l-4 border-gray-200 dark:border-gray-700 pl-4 text-xs text-gray-500 dark:text-gray-400">
                    <p class="leading-relaxed">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Nota:</span>
                        Equipo de soporte atiende los tickets según el orden de registro y el tipo de incidencia.
                        Si tienes información adicional sobre el mismo caso, agrégala en el detalle del ticket para
                        evitar duplicidades.
                    </p>
                </div>

                <div class="mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            Últimos tickets
                        </h3>

                        <a href="{{ route('tickets.index') }}" class="text-sm hover:underline text-inherit">
                            Ver todos
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2 pr-4 text-center whitespace-nowrap align-middle">#</th>
                                    <th class="py-2 pr-4 whitespace-nowrap align-middle">Asunto</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap align-middle">Estado</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap align-middle">Tiempo</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap align-middle">Técnico</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap align-middle">Creado</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($latestTickets as $t)
                                    @php
                                        $sem = $t->ageSemaphore();
                                    @endphp

                                    <tr>
                                        <td class="py-2 pr-4 text-center align-middle">{{ $t->id }}</td>

                                        <td class="py-2 pr-4 align-middle">
                                            <a class="hover:underline text-inherit"
                                                href="{{ route('tickets.show', $t) }}">
                                                {{ $t->subject }}
                                            </a>
                                        </td>

                                        <td class="py-2 pr-4 text-center align-middle">
                                            <x-status-badge :status="$t->status" />
                                        </td>

                                        <td class="py-2 pr-4 text-center align-middle">
                                            <x-time-pill :minutes="$sem['minutes']" :color="$sem['color']" />
                                        </td>

                                        <td class="py-2 pr-4 text-center align-middle">
                                            {{ $t->assignee?->name ?? '—' }}
                                        </td>

                                        <td class="py-2 pr-4 text-center align-middle">
                                            {{ $t->created_at?->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-gray-500 dark:text-gray-400">
                                            No hay tickets para mostrar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
