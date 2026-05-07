<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight
                   text-gray-800 dark:text-slate-200">
                Bandeja de Soporte
            </h2>


            <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold
                  bg-slate-100 text-slate-800 hover:bg-slate-200
                  dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @php
                $status = request('status');
                $filterLabel = \App\Models\Ticket::statusLabel($status);
            @endphp

            @if (!empty($filterLabel))
                <div class="mb-3">
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs
                   bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200
                   border border-gray-200 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">Filtrado:</span>
                        <strong class="font-semibold">{{ $filterLabel }}</strong>

                        <a href="{{ route('tickets.inbox') }}"
                            class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full
                      hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                            title="Quitar filtro">×</a>
                    </span>
                </div>
            @endif

            @if (session('ok'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded">
                    {{ session('ok') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div
                class="bg-white shadow-sm sm:rounded-lg p-6
            dark:bg-slate-900 dark:border dark:border-slate-800">
                @if ($tickets->isEmpty())
                    <p class="text-slate-600 dark:text-slate-300">No hay tickets en cola.</p>
                @else
                    <div class="overflow-x-auto">

                        @php
                            $hasOpen = $tickets->contains(fn($t) => $t->status === 'abierto');
                        @endphp

                        <table class="w-full text-sm table-fixed">

                            <colgroup>
                                <col class="w-16"> {{-- # --}}
                                <col> {{-- Asunto --}}
                                <col class="w-40"> {{-- Estado --}}
                                <col class="w-40"> {{-- Tiempo --}}
                                <col class="w-56"> {{-- Solicitante --}}
                                <col class="w-32"> {{-- Área --}}
                                <col class="w-56"> {{-- Asignado a --}}
                                @if ($hasOpen)
                                    <col class="w-[110px]"> {{-- Acción --}}
                                @endif
                            </colgroup>

                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700 dark:text-slate-200">
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">#</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Asunto</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Estado</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Tiempo</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Solicitante</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Área</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Asignado a</th>
                                    @if ($hasOpen)
                                        <th class="py-2 pr-4 text-center whitespace-nowrap">Acción</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $t)
                                    <tr
                                        class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">

                                        @php
                                            $age = $t->ageSemaphore();
                                        @endphp

                                        <td class="py-2 pr-4 text-center align-middle whitespace-nowrap">
                                            {{ $t->id }}</td>

                                        <td class="py-2 pr-4 align-middle">
                                            @php
                                                $myId = (int) auth()->id();
                                                $assignedToMe = (int) ($t->assigned_to ?? 0) === $myId;
                                                $canOpen = $t->status === 'abierto' || $assignedToMe;
                                            @endphp

                                            @if ($canOpen)
                                                <a href="{{ route('tickets.show', $t) }}"
                                                    class="text-slate-900 hover:underline dark:text-slate-100 block truncate"
                                                    title="{{ $t->subject }}">
                                                    {{ $t->subject }}
                                                </a>
                                            @else
                                                <span class="text-slate-800 dark:text-slate-200 block truncate"
                                                    title="{{ $t->subject }}">
                                                    {{ $t->subject }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-2 pr-4 align-middle text-center">
                                            <x-status-badge :status="$t->status" />
                                        </td>

                                        <td class="py-2 pr-4 align-middle text-center">
                                            <x-time-pill :minutes="$age['minutes']" :color="$age['color']" />
                                        </td>

                                        <td class="py-2 pr-4 align-middle">
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

                                        <td class="py-2 pr-4 align-middle text-center">
                                            <div class="truncate max-w-[180px]">
                                                {{ $t->creator?->area?->short_name ?? ($t->creator?->area?->name ?? '—') }}
                                            </div>
                                        </td>

                                        <td class="py-2 pr-4 align-middle">
                                            @if ($t->assignee)
                                                <div class="mx-auto truncate max-w-[220px] text-left">
                                                    {{ $t->assignee->name }}
                                                </div>
                                            @else
                                                <div class="mx-auto max-w-[220px] text-center">
                                                    <span class="text-slate-400 dark:text-slate-500">—</span>
                                                </div>
                                            @endif
                                        </td>

                                        @if ($hasOpen)
                                            <td class="py-2 pr-4 align-middle text-center whitespace-nowrap">
                                                @if ($t->status === 'abierto')
                                                    <form method="POST" action="{{ route('tickets.take', $t) }}">
                                                        @csrf
                                                        <button type="submit"
                                                            class="inline-flex items-center justify-center px-3 py-1 rounded-md text-xs font-semibold
                                                                bg-slate-900 text-white hover:bg-slate-800
                                                                dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                                            Tomar
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-slate-400 dark:text-slate-500">—</span>
                                                @endif
                                            </td>
                                        @endif
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
