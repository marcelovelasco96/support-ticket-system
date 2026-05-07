<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-slate-100 leading-tight">
            Panel del Técnico
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">

        {{-- Tarjetas resumen --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-5xl mx-auto">

            {{-- Cola --}}
            <a href="{{ route('tickets.inbox', ['status' => 'abierto']) }}"
                class="block p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500 transition border-l-4 border-l-blue-500 dark:border-l-blue-400">
                <div class="text-xs text-gray-500 dark:text-gray-400">Abierto</div>
                <div class="mt-1 text-2xl font-semibold">{{ $stats['cola'] ?? 0 }}</div>
            </a>

            {{-- En atención --}}
            <a href="{{ route('tickets.mywork', ['status' => 'en_proceso']) }}"
                class="block p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500 transition border-l-4 border-l-amber-500 dark:border-l-amber-400">
                <div class="text-xs text-gray-500 dark:text-gray-400">En atención</div>
                <div class="mt-1 text-2xl font-semibold">{{ $stats['atencion'] ?? 0 }}</div>
            </a>

            {{-- Finalizados --}}
            <a href="{{ route('tickets.history') }}"
                class="block p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500 transition border-l-4 border-l-green-500 dark:border-l-green-400">
                <div class="text-xs text-gray-500 dark:text-gray-400">Resuelto / Cerrado</div>
                <div class="mt-1 text-2xl font-semibold">{{ $stats['finalizados'] ?? 0 }}</div>
            </a>

        </div>

        @php
            $cola = (int) ($stats['cola'] ?? 0);
            $atencion = (int) ($stats['atencion'] ?? 0);
        @endphp

        @if ($cola > 0 || $atencion > 0)
            <div class="max-w-5xl mx-auto">
                <div
                    class="border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30 rounded-xl px-6 py-4
                    flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex-1">
                        <div class="font-semibold text-amber-900 dark:text-amber-200">
                            Hay {{ $cola }} tickets en cola y tienes {{ $atencion }} en atención.
                        </div>
                        <div class="text-sm text-amber-800 dark:text-amber-200">
                            Puedes tomar nuevos tickets desde la bandeja o continuar con los que ya estás atendiendo.
                        </div>
                    </div>

                    <a href="{{ route('tickets.inbox') }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold
                            bg-amber-600 text-white hover:bg-amber-700 whitespace-nowrap">
                        Ver cola
                    </a>
                </div>
            </div>
        @else
            <div class="max-w-5xl mx-auto">
                <div class="border border-green-200 bg-green-50 rounded-xl px-6 py-4">
                    <div class="font-semibold text-green-900">
                        🎉 No tienes tickets pendientes. ¡Buen trabajo!
                    </div>
                </div>
            </div>
        @endif

        {{-- Listas del técnico --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Por atender --}}
            <div id="por-atender"
                class="bg-white border border-slate-200 rounded-lg p-4 scroll-mt-24
            dark:bg-slate-900 dark:border-slate-800">
                <h4 class="font-semibold text-gray-900 dark:text-slate-100 mb-3">En atención (mis tickets)
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm table-auto">
                        <colgroup>
                            <col class="w-[60px]"> {{-- # --}}
                            <col class="w-[260px]"> {{-- Asunto --}}
                            <col class="w-[160px]"> {{-- Área --}}
                            <col class="w-[180px]"> {{-- Estado --}}
                            <col class="w-[180px]"> {{-- Tiempo --}}
                            <col class="w-[170px]"> {{-- Creado --}}
                            <col class="w-[120px]"> {{-- Acción --}}
                        </colgroup>

                        <thead>
                            <tr
                                class="border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300">
                                <th class="py-2 px-4 text-center whitespace-nowrap">#</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Asunto</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Área</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Estado</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Tiempo</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Creado</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($pending as $t)
                                @php $age = $t->ageSemaphore(); @endphp

                                <tr class="border-b border-slate-200 dark:border-slate-800">
                                    <td class="py-3 px-4 text-center align-middle whitespace-nowrap">{{ $t->id }}
                                    </td>

                                    <td class="py-3 px-4 align-middle text-left">
                                        <a class="font-semibold text-slate-900 hover:underline dark:text-slate-100 dark:hover:text-slate-50"
                                            href="{{ url('/tickets/' . $t->id) }}">
                                            {{ $t->subject ?? 'Ticket #' . $t->id }}
                                        </a>
                                    </td>

                                    <td class="py-3 px-4 align-middle text-center">
                                        <div class="truncate max-w-[160px]">
                                            {{ $t->creator?->area?->short_name ?? ($t->creator?->area?->name ?? '—') }}
                                        </div>
                                    </td>

                                    <td class="py-3 px-4 align-middle text-center">
                                        <x-status-badge :status="$t->status" />
                                    </td>

                                    <td class="py-3 px-4 align-middle text-center">
                                        <x-time-pill :minutes="$age['minutes']" :color="$age['color']" />
                                    </td>

                                    <td
                                        class="py-3 px-4 text-center text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        {{ optional($t->created_at)->diffForHumans() }}
                                    </td>

                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <a href="{{ url('/tickets/' . $t->id) }}"
                                            class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-sm font-semibold
                              bg-slate-900 text-white hover:bg-slate-800
                              dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                            Abrir
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-3 px-4 text-slate-500 dark:text-slate-400" colspan="6">Nada
                                        pendiente 🎉</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Últimos tickets --}}
            <div
                class="bg-white border border-slate-200 rounded-lg p-4
            dark:bg-slate-900 dark:border-slate-800">
                <h4 class="font-semibold text-gray-900 dark:text-slate-100 mb-3">Mis últimos finalizados</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm table-auto">
                        <colgroup>
                            <col class="w-[60px]"> {{-- # --}}
                            <col class="w-[260px]"> {{-- Asunto --}}
                            <col class="w-[160px]"> {{-- Área --}}
                            <col class="w-[180px]"> {{-- Estado --}}
                            <col class="w-[180px]"> {{-- Tiempo --}}
                            <col class="w-[170px]"> {{-- Actualizado --}}
                            <col class="w-[120px]"> {{-- Acción --}}
                        </colgroup>

                        <thead>
                            <tr
                                class="border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300">
                                <th class="py-2 px-4 text-center whitespace-nowrap">#</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Asunto</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Área</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Estado</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Tiempo</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Actualizado</th>
                                <th class="py-2 px-4 text-center whitespace-nowrap">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($recentFinalized as $t)
                                @php $age = $t->ageSemaphore(); @endphp

                                <tr class="border-b border-slate-200 dark:border-slate-800">
                                    <td class="py-3 px-4 text-center align-middle whitespace-nowrap">
                                        {{ $t->id }}</td>

                                    <td class="py-3 px-4 align-middle text-left">
                                        <a class="font-semibold text-slate-900 hover:underline dark:text-slate-100 dark:hover:text-slate-50"
                                            href="{{ url('/tickets/' . $t->id) }}">
                                            {{ $t->subject ?? 'Ticket #' . $t->id }}
                                        </a>
                                    </td>

                                    <td class="py-3 px-4 align-middle text-center">
                                        <div class="truncate max-w-[160px]">
                                            {{ $t->creator?->area?->short_name ?? ($t->creator?->area?->name ?? '—') }}
                                        </div>
                                    </td>

                                    <td class="py-3 px-4 align-middle text-center">
                                        <x-status-badge :status="$t->status" />
                                    </td>

                                    <td class="py-3 px-4 align-middle text-center">
                                        <x-time-pill :minutes="$age['minutes']" :color="$age['color']" />
                                    </td>

                                    <td
                                        class="py-3 px-4 text-center text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        {{ optional($t->updated_at)->diffForHumans() }}
                                    </td>

                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <a href="{{ url('/tickets/' . $t->id) }}"
                                            class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-sm font-semibold
                              bg-slate-100 text-slate-800 hover:bg-slate-200
                              dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-3 px-4 text-slate-500 dark:text-slate-400" colspan="6">Aún no hay
                                        tickets</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
