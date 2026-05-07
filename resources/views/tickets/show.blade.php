<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight
                   text-slate-900 dark:text-slate-100">
                Ticket #{{ $ticket->id }} - {{ $ticket->subject }}
            </h2>

            @php
                $u = auth()->user();
                $canTake = $u && $u->hasRole('tecnico') && $ticket->status === 'abierto' && empty($ticket->assigned_to);
            @endphp


        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-700 dark:text-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">Estado:</span>
                        <x-status-badge :status="$ticket->status" />

                        @if ($ticket->status === 'abierto' && is_null($ticket->assigned_to))
                            <span class="text-xs text-gray-500">(En cola)</span>
                        @endif
                    </div>

                    <div><span class="font-semibold">Categoría:</span> {{ $ticket->category }}</div>
                    <div><span class="font-semibold">Creado:</span> {{ $ticket->created_at }}</div>
                    <div><span class="font-semibold">Solicitante:</span> {{ $ticket->creator?->name }}</div>
                    <div>
                        <span class="font-semibold">Área:</span>
                        {{ $ticket->creator?->area?->name ?? '—' }}
                    </div>
                    <div><span class="font-semibold">Asignado a:</span> {{ $ticket->assignee?->name ?? '-' }}</div>
                    <div><span class="font-semibold">Resuelto:</span> {{ $ticket->resolved_at ?? '-' }}</div>
                    <div><span class="font-semibold">Cerrado:</span> {{ $ticket->closed_at ?? '-' }}</div>
                </div>

                @php
                    $now = now();

                    $created = $ticket->created_at;
                    $taken = $ticket->taken_at; // asumimos que existe si ya lo usas en bandeja
                    $end = $ticket->closed_at ?? $ticket->resolved_at;

                    $colaEnd = $taken ?? $now;
                    $attnStart = $taken;
                    $attnEnd = $end ?? $now;
                    $totalEnd = $end ?? $now;

                    $minsCola = $created ? $created->diffInMinutes($colaEnd) : null;
                    $minsAttn = $attnStart ? $attnStart->diffInMinutes($attnEnd) : null;
                    $minsTotal = $created ? $created->diffInMinutes($totalEnd) : null;

                    $fmt = function ($mins) {
                        if ($mins === null) {
                            return '—';
                        }
                        $d = intdiv($mins, 1440);
                        $mins %= 1440;
                        $h = intdiv($mins, 60);
                        $m = $mins % 60;
                        return ($d ? $d . 'd ' : '') . ($h ? $h . 'h ' : '') . $m . 'm';
                    };
                @endphp

                <div
                    class="mt-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-4">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Tiempos reales</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                        <div
                            class="rounded-md bg-white/70 dark:bg-slate-900/50 p-3 border border-slate-200 dark:border-slate-700">
                            <div class="text-xs text-slate-500">En cola</div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $fmt($minsCola) }}</div>
                        </div>

                        <div
                            class="rounded-md bg-white/70 dark:bg-slate-900/50 p-3 border border-slate-200 dark:border-slate-700">
                            <div class="text-xs text-slate-500">En atención</div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $fmt($minsAttn) }}</div>
                        </div>

                        <div
                            class="rounded-md bg-white/70 dark:bg-slate-900/50 p-3 border border-slate-200 dark:border-slate-700">
                            <div class="text-xs text-slate-500">Total</div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $fmt($minsTotal) }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="font-semibold mb-2">Descripción</h3>
                    <div
                        class="bg-gray-50 rounded-md border border-slate-200
                                dark:bg-slate-800 dark:border-slate-700">
                        <div
                            class="px-4 py-2 whitespace-pre-wrap text-sm leading-tight
                                text-slate-800 dark:text-slate-100">
                            {{ $ticket->description }}
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">
                        Adjuntos
                    </h3>

                    @if ($ticket->attachments->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">—</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($ticket->attachments as $a)
                                <li
                                    class="flex items-center justify-between gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                    <div class="min-w-0">
                                        @php
                                            $isImage = str_starts_with((string) ($a->mime ?? ''), 'image/');
                                        @endphp

                                        @if ($isImage)
                                            <img src="{{ route('tickets.attachments.view', $a) }}" alt="Adjunto"
                                                class="h-12 w-12 rounded object-cover border border-gray-200 dark:border-gray-700"
                                                loading="lazy" />
                                        @else
                                            <div
                                                class="h-12 w-12 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs text-gray-600 dark:text-gray-200">
                                                FILE
                                            </div>
                                        @endif
                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                            {{ $a->original_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ strtoupper($a->mime ?? 'ARCHIVO') }}
                                            ·
                                            {{ number_format(($a->size ?? 0) / 1024 / 1024, 2) }} MB
                                        </div>
                                    </div>

                                    <a href="{{ route('tickets.attachments.download', $a) }}"
                                        class="text-sm hover:underline text-inherit whitespace-nowrap">
                                        Descargar
                                    </a>

                                    @php
                                        $u = auth()->user();
                                        $isFinal = in_array($ticket->status, ['resuelto', 'cerrado'], true);
                                        $canDelete =
                                            $u &&
                                            ($u->hasRole('admin') ||
                                                $u->hasRole('tecnico') ||
                                                ((int) $ticket->created_by === (int) $u->id && !$isFinal));
                                    @endphp

                                    @if ($canDelete)
                                        <form method="POST" action="{{ route('tickets.attachments.destroy', $a) }}"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-sm text-red-600 dark:text-red-400 hover:underline whitespace-nowrap"
                                                onclick="return confirm('¿Eliminar este adjunto?');">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="mt-8">
                    <h3 class="font-semibold mb-2">Comentarios</h3>

                    <div class="space-y-4">
                        @forelse($ticket->comments as $c)
                            <div
                                class="p-3 rounded border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                                <div class="text-xs text-slate-600 dark:text-slate-300">
                                    {{ $c->user->name }} — {{ $c->created_at }}
                                </div>
                                <div class="mt-1 text-sm text-slate-800 dark:text-slate-100 whitespace-pre-wrap">
                                    {{ $c->comment }}
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No hay comentarios aún.</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-6">

                    {{-- TEXTAREA --}}
                    <form id="form-comment" method="POST" action="{{ route('tickets.comment', $ticket) }}">
                        @csrf
                        <textarea name="comment" rows="3"
                            class="w-full rounded-md border border-slate-300 dark:border-slate-700
                                bg-white dark:bg-slate-800
                                text-slate-900 dark:text-slate-100
                                placeholder:text-slate-400 dark:placeholder:text-slate-400
                                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                p-3"
                            placeholder="Agregar comentario..." required></textarea>
                    </form>

                    {{-- BARRA DE BOTONES --}}
                    <div class="mt-3 flex items-center justify-between">

                        {{-- BOTÓN COMENTAR --}}
                        <button type="submit" form="form-comment"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-md text-xs font-semibold uppercase tracking-widest
                   bg-[#00528e] text-white hover:bg-[#003f6d] shadow-sm hover:shadow transition">
                            Comentar
                        </button>

                        {{-- BOTÓN TOMAR --}}
                        @if ($canTake)
                            <form method="POST" action="{{ route('tickets.take', $ticket) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-5 py-2 rounded-md text-xs font-semibold uppercase tracking-widest
                           bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                    Tomar ticket
                                </button>
                            </form>
                        @endif

                    </div>

                </div>
            </div>

            <div class="mt-6 flex gap-2">
                @if ($ticket->status !== 'resuelto' && $ticket->status !== 'cerrado')

                    {{-- Admin puede resolver siempre --}}
                    @if (auth()->user()->hasRole('admin'))
                        <form method="POST" action="{{ route('tickets.resolve', $ticket) }}">
                            @csrf
                            <button type="submit"
                                style="background:#16a34a;color:#fff;padding:8px 12px;border-radius:6px;font-size:12px;font-weight:600;">
                                Marcar Resuelto
                            </button>
                        </form>
                    @endif

                    {{-- Técnico SOLO si está asignado --}}
                    @if (auth()->user()->hasRole('tecnico') && (int) $ticket->assigned_to === (int) auth()->id())
                        <form method="POST" action="{{ route('tickets.resolve', $ticket) }}">
                            @csrf
                            <button type="submit"
                                style="background:#16a34a;color:#fff;padding:8px 12px;border-radius:6px;font-size:12px;font-weight:600;">
                                Marcar Resuelto
                            </button>
                        </form>
                    @endif

                    @if ($ticket->status === 'en_proceso')
                        @if (auth()->user()->hasRole('admin') ||
                                (auth()->user()->hasRole('tecnico') && (int) $ticket->assigned_to === (int) auth()->id()))
                            <form method="POST" action="{{ route('tickets.release', $ticket) }}">
                                @csrf
                                <button type="submit"
                                    style="background:#f59e0b;color:#111827;padding:8px 12px;border-radius:6px;font-size:12px;font-weight:700;">
                                    Devolver a cola
                                </button>
                            </form>
                        @endif
                    @endif


                @endif


                @if (auth()->user()->hasRole('admin'))
                    @if ($ticket->status !== 'cerrado')
                        <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                            @csrf
                            <button type="submit"
                                style="background:#111827;color:#fff;padding:8px 12px;border-radius:6px;font-size:12px;font-weight:600;">
                                Cerrar Ticket
                            </button>
                        </form>
                    @endif
                @endif
            </div>

        </div>

    </div>
    </div>
</x-app-layout>
