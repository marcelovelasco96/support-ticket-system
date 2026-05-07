<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketComment;
use Illuminate\Support\Facades\DB;
use App\Mail\TicketCreated;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function create()
    {
        $categories = ['Hardware', 'Software', 'Red', 'Accesos', 'Otros'];
        $priorities = ['Baja', 'Media', 'Alta', 'Crítica'];

        return view('tickets.create', compact('categories', 'priorities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string'],

            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ]);

        $ticket = Ticket::create([
            'created_by' => auth()->id(),
            'assigned_to' => null,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'category' => $data['category'],
            'status' => 'abierto',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store("tickets/{$ticket->id}", 'local');

                $ticket->attachments()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        if (auth()->user()?->email) {
            Mail::to(auth()->user()->email)->queue(new TicketCreated($ticket));
        }

        return redirect()->route('tickets.create')->with('ok', 'Ticket registrado correctamente.');
    }

    public function index()
    {

        $q = \App\Models\Ticket::where('created_by', auth()->id());

        // Nuevo filtro oficial por status (con compat temporal phase->status)
        $statuses = $this->requestedStatuses();
        if ($statuses) {
            $q->whereIn('status', $statuses);
        }

        $tickets = $q->orderByDesc('id')->get();

        return view('tickets.index', compact('tickets'));
    }

    public function inbox()
    {

        $allowed = ['abierto', 'en_proceso', 'finalizados', 'resuelto', 'cerrado'];

        if (request()->has('status') && !in_array(request('status'), $allowed, true)) {
            return redirect()->route('tickets.inbox');
        }

        $phase = request('phase');
        $status = request('status');

        if ($phase && !$status) {
            $status = match ($phase) {
                'COLA' => 'abierto',
                'PROC' => 'en_proceso',
                'FIN'  => 'finalizados',
                default => null,
            };

            return redirect()->route('tickets.inbox', $status ? ['status' => $status] : []);
        }

        $statuses = $this->requestedStatuses() ?? ['abierto', 'en_proceso'];

        $tickets = \App\Models\Ticket::with(['creator.area', 'assignee'])
            ->whereIn('status', $statuses)
            ->orderByRaw("CASE WHEN status = 'abierto' THEN 0 ELSE 1 END ASC")
            ->orderByRaw("CASE WHEN status = 'en_proceso' AND taken_at IS NOT NULL THEN taken_at ELSE created_at END ASC")
            ->get();

        return view('tickets.inbox', compact('tickets'));
    }

    public function take(\App\Models\Ticket $ticket)
    {
        // Solo permitir tomar si está abierto
        if ($ticket->status !== 'abierto') {
            return back()->withErrors(['msg' => 'Este ticket ya no está disponible para tomar.']);
        }

        $ticket->assigned_to = auth()->id();
        $ticket->status      = 'en_proceso';

        // Primera respuesta: solo se setea una vez
        if (is_null($ticket->taken_at)) {
            $ticket->taken_at = now();
        }

        $ticket->save();

        return back()->with('ok', "Ticket #{$ticket->id} tomado correctamente.");
    }


    public function show(\App\Models\Ticket $ticket)
    {
        $user = auth()->user();

        // Control de acceso
        if ($user->hasRole('admin')) {
            // ok
        } elseif ($user->hasRole('tecnico')) {

            $isAssignedToMe = (int)$ticket->assigned_to === (int)$user->id;
            $isInActiveQueue = in_array($ticket->status, ['abierto', 'en_proceso']);

            // T�cnico puede ver:
            // - cualquier ticket asignado a �l (incluye resuelto/cerrado)
            // - y tambi�n la cola activa (abierto/en_proceso)
            if (!($isAssignedToMe || $isInActiveQueue)) {
                abort(403);
            }
        } else {
            // usuario normal: solo sus tickets (comparaci�n segura)
            if ((int) $ticket->created_by !== (int) $user->id) {
                abort(403);
            }
        }

        $ticket->load(['creator.area', 'assignee', 'comments.user', 'attachments']);

        return view('tickets.show', compact('ticket'));
    }


    public function resolve(\App\Models\Ticket $ticket)
    {
        $user = auth()->user();

        if (!($user->hasRole('admin') || $user->hasRole('tecnico'))) {
            abort(403);
        }

        // T�cnico solo puede resolver si est� asignado a �l (comparaci�n segura)
        if ($user->hasRole('tecnico') && (int)$ticket->assigned_to !== (int)$user->id) {
            abort(403);
        }

        if (in_array($ticket->status, ['resuelto', 'cerrado'])) {
            return back()->withErrors(['msg' => 'Este ticket ya no puede ser resuelto.']);
        }

        $ticket->update([
            'status' => 'resuelto',
            'resolved_at' => now(),
        ]);

        return back()->with('ok', "Ticket #{$ticket->id} marcado como resuelto.");
    }

    public function close(\App\Models\Ticket $ticket)
    {
        $user = auth()->user();

        // Solo admin cierra (control municipal)
        if (!$user->hasRole('admin')) {
            abort(403);
        }

        if ($ticket->status === 'cerrado') {
            return back()->withErrors(['msg' => 'Este ticket ya est� cerrado.']);
        }

        $ticket->update([
            'status' => 'cerrado',
            'closed_at' => now(),
        ]);

        return back()->with('ok', "Ticket #{$ticket->id} cerrado.");
    }

    public function comment(Request $request, \App\Models\Ticket $ticket)
    {
        // Reusar la misma regla de acceso del show()
        $this->show($ticket); // si no tiene acceso, abort(403)

        $request->validate([
            'comment' => ['required', 'string'],
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return back()->with('ok', 'Comentario agregado.');
    }

    public function myWork()
    {

        if (request()->has('status') || request()->has('phase')) {
            return redirect()->route('tickets.mywork');
        }

        $tickets = Ticket::with(['creator.area', 'assignee'])
            ->where('assigned_to', auth()->id())
            ->where('status', 'en_proceso')
            ->orderByDesc('id')
            ->get();

        return view('tickets.mywork', compact('tickets'));
    }

    public function release(\App\Models\Ticket $ticket)
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            // ok
        } elseif ($user->hasRole('tecnico')) {
            if ((int)$ticket->assigned_to !== (int)$user->id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        // Solo permitir devolver si est� en proceso
        if ($ticket->status !== 'en_proceso') {
            return back()->withErrors(['msg' => 'Solo puedes devolver tickets que est�n en proceso.']);
        }

        $ticket->update([
            'status' => 'abierto',
            'taken_at' => null,
            'assigned_to' => null,
        ]);

        return back()->with('ok', "Ticket #{$ticket->id} devuelto a cola.");
    }

    public function history()
    {
        $tickets = \App\Models\Ticket::with(['creator.area', 'assignee'])
            ->where('assigned_to', auth()->id())
            ->whereIn('status', ['resuelto', 'cerrado'])
            ->orderByDesc('id')
            ->get();

        return view('tickets.history', compact('tickets'));
    }

    public function slaDashboard()
    {
        $now = now();

        // Mapa SLA por prioridad (igual que el componente)
        $slaHoursByPriority = [
            'critica' => 2,
            'alta'    => 4,
            'media'   => 24,
            'baja'    => 72,
        ];

        // Función para normalizar prioridad (robusta contra "Cr�tica")
        $normalizePriority = function (?string $raw) {
            $p = mb_strtolower(trim((string)$raw), 'UTF-8');
            $p = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'u', 'n'], $p);
            $p = preg_replace('/[^a-z]/', '', $p); // deja solo letras
            return $p ?: 'media';
        };

        // Traemos tickets relevantes
        $tickets = \App\Models\Ticket::select('id', 'priority', 'status', 'created_at', 'taken_at', 'resolved_at', 'closed_at', 'assigned_to')
            ->get();

        $total = $tickets->count();

        $openCount = $tickets->where('status', 'abierto')->count();
        $inProcessCount = $tickets->where('status', 'en_proceso')->count();
        $resolvedCount = $tickets->where('status', 'resuelto')->count();
        $closedCount = $tickets->where('status', 'cerrado')->count();

        $onTime = 0;
        $late = 0;

        // Promedios de resolución por prioridad (solo resueltos/cerrados)
        $resolutionMinutesByPriority = [
            'critica' => [],
            'alta' => [],
            'media' => [],
            'baja' => [],
        ];

        $evaluated = 0;

        foreach ($tickets as $t) {
            $p = $normalizePriority($t->priority);
            $limitHours = $slaHoursByPriority[$p] ?? 24;
            $limitMinutes = $limitHours * 60;

            // Rango de cálculo SLA (igual que sla-pill)
            $createdAt = $t->created_at ? \Carbon\Carbon::parse($t->created_at) : null;
            $takenAt = $t->taken_at ? \Carbon\Carbon::parse($t->taken_at) : null;
            $resolvedAt = $t->resolved_at ? \Carbon\Carbon::parse($t->resolved_at) : null;
            $closedAt = $t->closed_at ? \Carbon\Carbon::parse($t->closed_at) : null;

            if ($t->status === 'abierto') {
                $from = $createdAt;
                $to = $now;
            } else {
                $from = $takenAt ?: $createdAt;
                $to = $closedAt ?: ($resolvedAt ?: $now);
            }

            // Solo evaluar SLA final en resueltos/cerrados
            if (!in_array($t->status, ['resuelto', 'cerrado'], true)) {
                continue;
            }

            $minutes = ($from && $to) ? $from->diffInMinutes($to) : 0;

            $evaluated++;

            if ($minutes <= $limitMinutes) $onTime++;
            else $late++;

            // Si ya está resuelto/cerrado, guardamos resolución (tomado->resuelto/cerrado)
            if (in_array($t->status, ['resuelto', 'cerrado'], true)) {
                $fromRes = $takenAt ?: $createdAt;
                $toRes = $closedAt ?: ($resolvedAt ?: null);
                if ($fromRes && $toRes) {
                    $resolutionMinutesByPriority[$p][] = $fromRes->diffInMinutes($toRes);
                }
            }
        }

        //$pctOnTime = 85;
        $pctOnTime = $evaluated ? round(($onTime / $evaluated) * 100, 1) : 0;
        $pctLate   = $evaluated ? round(($late / $evaluated) * 100, 1) : 0;

        // Estado general del SLA (semáforo)
        if ($pctOnTime >= 80) {
            $slaStatus = 'ok';        // verde
            $slaText = 'SLA saludable';
        } elseif ($pctOnTime >= 60) {
            $slaStatus = 'warning';   // amarillo
            $slaText = 'SLA en observación';
        } else {
            $slaStatus = 'danger';    // rojo
            $slaText = 'SLA en riesgo';
        }

        $avgResolutionHoursByPriority = [];
        foreach ($resolutionMinutesByPriority as $p => $minsArr) {
            if (count($minsArr) === 0) {
                $avgResolutionHoursByPriority[$p] = null;
            } else {
                $avgMinutes = array_sum($minsArr) / count($minsArr);
                $avgResolutionHoursByPriority[$p] = round($avgMinutes / 60, 2);
            }
        }

        // Conteo por técnico (assigned_to)
        $byTech = \App\Models\Ticket::select('assigned_to', \DB::raw('count(*) as total'))
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->orderByDesc('total')
            ->get();

        // Traemos nombres
        $techIds = $byTech->pluck('assigned_to')->all();
        $techNames = \App\Models\User::whereIn('id', $techIds)->pluck('name', 'id');

        $topTech = $byTech->map(function ($row) use ($techNames) {
            return [
                'tech_id' => $row->assigned_to,
                'tech_name' => $techNames[$row->assigned_to] ?? ('Técnico #' . $row->assigned_to),
                'total' => (int)$row->total,
            ];
        });

        // Productividad por técnico: resueltos + cerrados
        $doneByTech = \App\Models\Ticket::select('assigned_to', DB::raw('count(*) as total_done'))
            ->whereNotNull('assigned_to')
            ->whereIn('status', ['resuelto', 'cerrado'])
            ->groupBy('assigned_to')
            ->orderByDesc('total_done')
            ->get();

        $doneTechIds = $doneByTech->pluck('assigned_to')->all();
        $doneTechNames = \App\Models\User::whereIn('id', $doneTechIds)->pluck('name', 'id');

        $topDoneTech = $doneByTech->map(function ($row) use ($doneTechNames) {
            return [
                'tech_id' => $row->assigned_to,
                'tech_name' => $doneTechNames[$row->assigned_to] ?? ('Técnico #' . $row->assigned_to),
                'total_done' => (int)$row->total_done,
            ];
        });

        // SLA por técnico: a tiempo vs atrasado (para tickets asignados)
        $slaByTech = [];

        $assignedTickets = \App\Models\Ticket::select('id', 'priority', 'status', 'created_at', 'taken_at', 'resolved_at', 'closed_at', 'assigned_to')
            ->whereNotNull('assigned_to')
            ->get();

        foreach ($assignedTickets as $t) {
            $techId = (string)$t->assigned_to;

            if (!isset($slaByTech[$techId])) {
                $slaByTech[$techId] = [
                    'tech_id' => $techId,
                    'on_time' => 0,
                    'late' => 0,
                    'total' => 0,
                ];
            }

            $p = $normalizePriority($t->priority);
            $limitHours = $slaHoursByPriority[$p] ?? 24;
            $limitMinutes = $limitHours * 60;

            $createdAt = $t->created_at ? \Carbon\Carbon::parse($t->created_at) : null;
            $takenAt = $t->taken_at ? \Carbon\Carbon::parse($t->taken_at) : null;
            $resolvedAt = $t->resolved_at ? \Carbon\Carbon::parse($t->resolved_at) : null;
            $closedAt = $t->closed_at ? \Carbon\Carbon::parse($t->closed_at) : null;

            // Mismo criterio que venimos usando:
            // - si está abierto: created->now
            // - si ya fue tomado o está en proceso/resuelto/cerrado: taken(or created)->(closed or resolved or now)
            if ($t->status === 'abierto') {
                $from = $createdAt;
                $to = $now;
            } else {
                $from = $takenAt ?: $createdAt;
                $to = $closedAt ?: ($resolvedAt ?: $now);
            }

            $minutes = ($from && $to) ? $from->diffInMinutes($to) : 0;

            $slaByTech[$techId]['total']++;

            if ($minutes <= $limitMinutes) $slaByTech[$techId]['on_time']++;
            else $slaByTech[$techId]['late']++;
        }

        // Convertimos a colección y le añadimos nombre
        $slaByTech = collect(array_values($slaByTech));

        $slaTechIds = $slaByTech->pluck('tech_id')->map(fn($v) => (int)$v)->all();
        $slaTechNames = \App\Models\User::whereIn('id', $slaTechIds)->pluck('name', 'id');

        $slaByTech = $slaByTech->map(function ($row) use ($slaTechNames) {
            $id = (int)$row['tech_id'];
            $total = (int)$row['total'];
            $onTime = (int)$row['on_time'];
            $late = (int)$row['late'];

            return [
                'tech_id' => $id,
                'tech_name' => $slaTechNames[$id] ?? ('Técnico #' . $id),
                'total' => $total,
                'on_time' => $onTime,
                'late' => $late,
                'pct_on_time' => $total ? round(($onTime / $total) * 100, 1) : 0,
                'pct_late' => $total ? round(($late / $total) * 100, 1) : 0,
            ];
        })->sortByDesc('pct_on_time')->values();

        return view('dashboards.sla', [
            'total' => $total,
            'openCount' => $openCount,
            'inProcessCount' => $inProcessCount,
            'resolvedCount' => $resolvedCount,
            'closedCount' => $closedCount,
            'onTime' => $onTime,
            'late' => $late,
            'pctOnTime' => $pctOnTime,
            'pctLate' => $pctLate,
            'avgResolutionHoursByPriority' => $avgResolutionHoursByPriority,
            'topTech' => $topTech,
            'topDoneTech' => $topDoneTech,
            'slaByTech' => $slaByTech,
            'slaStatus' => $slaStatus,
            'slaText'   => $slaText,
        ]);
    }

    public function adminTickets(\Illuminate\Http\Request $request)
    {
        $techId = $request->query('tecnico');
        $status = $request->query('status');
        $order  = $request->query('order', 'recientes'); // default

        $q = \App\Models\Ticket::query()
            ->with(['creator', 'assignee']);

        // Filtro: Técnico
        if (!empty($techId)) {
            $q->where('assigned_to', $techId);
        }

        // Filtro: Estado (usa el mismo helper que ya unificamos)
        if (!empty($status)) {
            $statuses = $this->requestedStatuses();
            if ($statuses) {
                $q->whereIn('status', $statuses);
            }
        }

        // Ordenar por
        if ($order === 'antiguos') {
            $q->orderBy('created_at', 'asc');
        } elseif ($order === 'tiempo_desc') {
            // Mayor tiempo primero (misma lógica de "Tiempo" del sistema)
            $q->orderByRaw("
        CASE
            WHEN status = 'en_proceso' AND taken_at IS NOT NULL
                THEN DATEDIFF(MINUTE, taken_at, GETDATE())
            ELSE DATEDIFF(MINUTE, created_at, GETDATE())
        END DESC
    ")->orderBy('created_at', 'asc'); // desempate estable
        } elseif ($order === 'tiempo_asc') {
            // Menor tiempo primero (misma lógica de "Tiempo" del sistema)
            $q->orderByRaw("
        CASE
            WHEN status = 'en_proceso' AND taken_at IS NOT NULL
                THEN DATEDIFF(MINUTE, taken_at, GETDATE())
            ELSE DATEDIFF(MINUTE, created_at, GETDATE())
        END ASC
    ")->orderByDesc('created_at'); // desempate estable
        } else {
            // recientes
            $q->orderByDesc('created_at');
        }

        $tickets = $q->paginate(20)->withQueryString();

        $tecnico = null;

        if (!empty($techId)) {
            $tecnico = \App\Models\User::find($techId);
        }

        // Para la vista (filtros seleccionados)
        return view('admin.tickets', compact('tickets', 'techId', 'status', 'order', 'tecnico'));
    }

    private function requestedStatuses(): ?array
    {
        // Nuevo parámetro oficial
        $status = request('status');
        if ($status) {
            return match ($status) {
                'abierto' => ['abierto'],
                'en_proceso' => ['en_proceso'],
                'resuelto' => ['resuelto'],
                'cerrado' => ['cerrado'],
                'finalizados' => ['resuelto', 'cerrado'],
                default => null,
            };
        }

        return null;
    }
}
