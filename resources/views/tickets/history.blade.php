<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-slate-200">
            Historial de Soporte (Resueltos / Cerrados)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 dark:bg-slate-900 dark:border dark:border-slate-800">

                @if ($tickets->isEmpty())
                    <p class="text-slate-600 dark:text-slate-300">Aún no tienes tickets resueltos o cerrados.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm table-auto">

                            <colgroup>
                                <col class="w-[60px]"> {{-- # --}}
                                <col class="w-[260px]"> {{-- Asunto --}}
                                <col class="w-[150px]"> {{-- Estado --}}
                                <col class="w-[180px]"> {{-- Tiempo --}}
                                <col class="w-[260px]"> {{-- Solicitante --}}
                                <col class="w-[200px]"> {{-- Área --}}
                                <col class="w-[160px]"> {{-- Resuelto --}}
                                <col class="w-[160px]"> {{-- Cerrado --}}
                            </colgroup>

                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700 dark:text-slate-200">
                                    <th class="py-2 px-4 text-center whitespace-nowrap">#</th>
                                    <th class="py-2 px-4 text-center whitespace-nowrap">Asunto</th>
                                    <th class="py-2 px-4 text-center whitespace-nowrap">Estado</th>
                                    <th class="py-2 px-4 text-center whitespace-nowrap">Tiempo</th>
                                    <th class="py-2 px-4 text-center whitespace-nowrap">Solicitante</th>
                                    <th class="py-2 px-4 text-center whitespace-nowrap">Área</th>
                                    <th class="py-2 px-4 text-center whitespace-nowrap">Resuelto</th>
                                    <th class="py-2 px-4 text-center whitespace-nowrap">Cerrado</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($tickets as $t)
                                    @php
                                        $age = $t->ageSemaphore();
                                    @endphp

                                    <tr
                                        class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                        <td class="py-3 px-4 text-center align-middle whitespace-nowrap">
                                            {{ $t->id }}
                                        </td>

                                        <td class="py-3 px-4 align-middle">
                                            <a href="{{ route('tickets.show', $t) }}"
                                                class="text-slate-900 hover:underline dark:text-slate-100">
                                                {{ $t->subject }}
                                            </a>
                                        </td>

                                        <td class="py-3 px-4 align-middle text-center">
                                            <x-status-badge :status="$t->status" />
                                        </td>

                                        <td class="py-3 px-4 align-middle text-center">
                                            <x-time-pill :minutes="$age['minutes']" :color="$age['color']" />
                                        </td>

                                        <td class="py-3 px-4 align-middle">
                                            @if ($t->creator?->name)
                                                <div class="mx-auto truncate max-w-[220px] text-left">
                                                    {{ $t->creator->name }}
                                                </div>
                                            @else
                                                <div class="mx-auto max-w-[220px] text-center">
                                                    <span class="text-slate-400 dark:text-slate-500">—</span>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="py-3 px-4 align-middle text-center">
                                            <div class="truncate max-w-[180px]">
                                                {{ $t->creator?->area?->short_name ?? ($t->creator?->area?->name ?? '—') }}
                                            </div>
                                        </td>

                                        <td class="py-3 px-4 align-middle text-center whitespace-nowrap">
                                            {{ $t->resolved_at ? $t->resolved_at->diffForHumans() : '—' }}
                                        </td>

                                        <td class="py-3 px-4 align-middle text-center whitespace-nowrap">
                                            {{ $t->closed_at ? $t->closed_at->diffForHumans() : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
