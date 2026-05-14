<x-app-layout>

    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">

            {{-- IZQUIERDA --}}
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Mis Tickets
                </h2>
            </div>



        </div>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @php
                $status = request('status');
                $filterLabel = \App\Models\Ticket::statusLabel($status);
            @endphp

            <x-filter-pill :label="$filterLabel" :clear-url="route('tickets.index')" />

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">

                @if ($tickets->isEmpty())
                    <p class="text-gray-600 dark:text-gray-300">Aún no has registrado tickets.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm table-fixed">

                            <colgroup>
                                <col class="w-16">
                                <col>
                                <col class="w-28">
                                <col class="w-32">
                                <col class="w-32">
                                <col class="w-56">
                                <col class="w-36">
                            </colgroup>


                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">#</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Asunto</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Categoría</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Estado</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Tiempo</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Técnico</th>
                                    <th class="py-2 pr-4 text-center whitespace-nowrap">Creado</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($tickets as $t)
                                    @php
                                        $age = $t->ageSemaphore();
                                    @endphp

                                    <tr class="border-b">
                                        <td class="py-2 pr-4 text-center align-middle">{{ $t->id }}</td>
                                        <td class="py-2 pr-4 align-middle">
                                            <a class="hover:underline text-inherit"
                                                href="{{ route('tickets.show', $t) }}">
                                                {{ $t->subject }}
                                            </a>
                                        </td>
                                        <td class="py-2 pr-4 text-center align-middle">{{ $t->category }}</td>

                                        <td class="py-2 pr-4 align-middle text-center">
                                            <x-status-badge :status="$t->status" />
                                        </td>

                                        <td class="py-2 pr-4 align-middle">
                                            <x-time-pill :minutes="$age['minutes']" :color="$age['color']" />
                                        </td>

                                        <td class="py-2 pr-4 text-center align-middle">
                                            {{ $t->assignee?->name ?? '—' }}
                                        </td>

                                        <td class="py-2 pr-4 text-center align-middle whitespace-nowrap">
                                            {{ $t->created_at?->diffForHumans() }}
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
