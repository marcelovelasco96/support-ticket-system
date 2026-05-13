<x-app-layout>
    <x-slot name="header">
        @php
            $u = auth()->user();
            $canTake = $u && $u->hasRole('tecnico') && $ticket->status === 'abierto' && empty($ticket->assigned_to);
        @endphp

        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                        Ticket #{{ $ticket->id }}
                    </span>

                    <x-status-badge :status="$ticket->status" />
                </div>

                <h2 class="mt-2 font-semibold text-xl leading-tight text-slate-900 dark:text-slate-100">
                    {{ $ticket->subject }}
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Creado por {{ $ticket->creator?->name ?? '—' }}
                    · {{ $ticket->created_at?->translatedFormat('d M Y · H:i') }}
                </p>
            </div>

            <div class="shrink-0 flex flex-wrap justify-end gap-2">

                @if ($ticket->status !== 'resuelto' && $ticket->status !== 'cerrado')

                    @if (auth()->user()->hasRole('admin') ||
                            (auth()->user()->hasRole('tecnico') && (int) $ticket->assigned_to === (int) auth()->id()))
                        <form method="POST" action="{{ route('tickets.resolve', $ticket) }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold
                    bg-emerald-600 text-white hover:bg-emerald-700
                    transition shadow-sm">
                                Marcar resuelto
                            </button>
                        </form>
                    @endif

                    @if ($ticket->status === 'en_proceso')
                        @if (auth()->user()->hasRole('admin') ||
                                (auth()->user()->hasRole('tecnico') && (int) $ticket->assigned_to === (int) auth()->id()))
                            <form method="POST" action="{{ route('tickets.release', $ticket) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold
                        bg-amber-500 text-slate-900 hover:bg-amber-400
                        transition shadow-sm">
                                    Devolver a cola
                                </button>
                            </form>
                        @endif
                    @endif

                @endif

                @if (auth()->user()->hasRole('admin') && $ticket->status !== 'cerrado')
                    <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold
                border border-slate-300 text-slate-700 bg-white hover:bg-slate-50
                dark:border-slate-600 dark:text-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800
                transition shadow-sm">
                            Cerrar ticket
                        </button>
                    </form>
                @endif

                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold
        bg-white text-slate-700 hover:bg-slate-50
        dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700
        border border-slate-200 dark:border-slate-700 shadow-sm transition">
                    Volver
                </a>
            </div>
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
                <div
                    class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4 text-sm">

                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Categoría</div>
                            <div class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
                                {{ $ticket->category }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Solicitante</div>
                            <div class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
                                {{ $ticket->creator?->name ?? '—' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Área</div>
                            <div class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
                                {{ $ticket->creator?->area?->name ?? '—' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Asignado a</div>
                            <div class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
                                {{ $ticket->assignee?->name ?? 'Sin asignar' }}
                            </div>
                        </div>

                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-700">

                        <div class="flex flex-wrap items-start gap-y-4">

                            {{-- Creado --}}
                            <div class="flex items-start gap-3 flex-1 min-w-[140px]">

                                <div class="mt-1.5 h-3 w-3 rounded-full bg-blue-500"></div>

                                <div>
                                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        Creado
                                    </div>

                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $ticket->created_at?->translatedFormat('d M Y · H:i') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Línea --}}
                            <div class="hidden md:block flex-1 h-px opacity-0 mt-4"></div>

                            {{-- Tomado --}}
                            <div class="flex items-start gap-3 flex-1 min-w-[140px]">

                                <div class="mt-1.5 h-3 w-3 rounded-full bg-amber-500"></div>

                                <div>
                                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        Tomado
                                    </div>

                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $ticket->taken_at?->translatedFormat('d M Y · H:i') ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="hidden md:block flex-1 h-px opacity-0 mt-4"></div>

                            {{-- Resuelto --}}
                            <div class="flex items-start gap-3 flex-1 min-w-[140px]">

                                <div class="mt-1.5 h-3 w-3 rounded-full bg-emerald-500"></div>

                                <div>
                                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        Resuelto
                                    </div>

                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $ticket->resolved_at?->translatedFormat('d M Y · H:i') ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="hidden md:block flex-1 h-px opacity-0 mt-4"></div>

                            {{-- Cerrado --}}
                            <div class="flex items-start gap-3 flex-1 min-w-[140px]">

                                <div class="mt-1.5 h-3 w-3 rounded-full bg-slate-500"></div>

                                <div>
                                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        Cerrado
                                    </div>

                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $ticket->closed_at?->translatedFormat('d M Y · H:i') ?? '—' }}
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
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

                <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-4">

                    {{-- TIEMPOS --}}
                    <div
                        class="xl:col-span-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 p-5">

                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-4">
                            Tiempos reales
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">

                            <div
                                class="rounded-lg bg-white dark:bg-slate-900/60 p-4 border border-slate-200 dark:border-slate-700">
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    En cola
                                </div>

                                <div class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $fmt($minsCola) }}
                                </div>
                            </div>

                            <div
                                class="rounded-lg bg-white dark:bg-slate-900/60 p-4 border border-slate-200 dark:border-slate-700">
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    En atención
                                </div>

                                <div class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $fmt($minsAttn) }}
                                </div>
                            </div>

                            <div
                                class="rounded-lg bg-white dark:bg-slate-900/60 p-4 border border-slate-200 dark:border-slate-700">
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    Total
                                </div>

                                <div class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $fmt($minsTotal) }}
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ADJUNTOS --}}
                    <div
                        class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 p-5">

                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-4">
                            Adjuntos
                        </h3>

                        @if ($ticket->attachments->isEmpty())
                            <p class="text-sm text-slate-500 dark:text-slate-400">No se adjuntaron evidencias.</p>
                        @else
                            <div class="space-y-3">

                                @foreach ($ticket->attachments as $a)
                                    @php
                                        $isImage = str_starts_with((string) ($a->mime ?? ''), 'image/');
                                    @endphp

                                    <a href="{{ route('tickets.attachments.view', $a) }}" target="_blank"
                                        class="group flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700
                                                bg-white dark:bg-slate-900/60 p-3
                                                hover:border-blue-300 dark:hover:border-blue-700
                                                hover:bg-slate-50 dark:hover:bg-slate-800 transition">

                                        @if ($isImage)
                                            <img src="{{ route('tickets.attachments.view', $a) }}" alt="Adjunto"
                                                class="h-12 w-12 rounded-lg object-cover border border-slate-200 dark:border-slate-700"
                                                loading="lazy" />
                                        @else
                                            <div
                                                class="h-12 w-12 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-semibold text-slate-500 dark:text-slate-300">
                                                FILE
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <div
                                                class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                                {{ $a->original_name }}
                                            </div>

                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                Ver adjunto · {{ number_format(($a->size ?? 0) / 1024 / 1024, 2) }} MB
                                            </div>
                                        </div>

                                    </a>
                                @endforeach

                            </div>
                        @endif

                    </div>

                </div>

                <div
                    class="mt-6 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Descripción</h3>
                    </div>

                    <div
                        class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/50 p-4">
                        <div class="whitespace-pre-wrap text-sm leading-relaxed text-slate-800 dark:text-slate-100">
                            {{ $ticket->description }}
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                            Comentarios
                        </h3>

                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $ticket->comments->count() }} comentario(s)
                        </span>
                    </div>

                    <div class="space-y-3">

                        @forelse($ticket->comments as $c)
                            <div class="flex gap-3">

                                {{-- Avatar --}}
                                <div
                                    class="shrink-0 h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-700
                                            flex items-center justify-center text-sm font-semibold
                                            text-slate-700 dark:text-slate-200">

                                    {{ strtoupper(substr($c->user->name ?? '?', 0, 1)) }}
                                </div>

                                {{-- Contenido --}}
                                <div class="flex-1 min-w-0">

                                    <div
                                        class="rounded-2xl border border-slate-200 dark:border-slate-700
                                            bg-white dark:bg-slate-800/70 px-4 py-3">

                                        <div class="flex items-center justify-between gap-3">

                                            <div class="font-semibold text-sm text-slate-900 dark:text-slate-100">
                                                {{ $c->user->name }}
                                            </div>

                                            <div class="text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">

                                                @if ($c->created_at?->isToday())
                                                    Hoy · {{ $c->created_at->format('H:i') }}
                                                @elseif ($c->created_at?->isYesterday())
                                                    Ayer · {{ $c->created_at->format('H:i') }}
                                                @else
                                                    {{ $c->created_at?->translatedFormat('d M Y · H:i') }}
                                                @endif

                                            </div>

                                        </div>

                                        <div
                                            class="mt-2 whitespace-pre-wrap text-sm leading-normal
                                                text-slate-800 dark:text-slate-100">
                                            {{ $c->comment }}
                                        </div>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div
                                class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700
                p-6 text-center text-sm text-slate-500 dark:text-slate-400">

                                No hay comentarios aún.

                            </div>
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
        </div>
    </div>
    </div>
</x-app-layout>
