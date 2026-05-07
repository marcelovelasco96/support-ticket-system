<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $stats = [
                'total'      => Ticket::count(),
                'abierto'     => Ticket::where('status', 'abierto')->count(),
                'en_proceso'  => Ticket::where('status', 'en_proceso')->count(),
                'resuelto'    => Ticket::where('status', 'resuelto')->count(),
                'cerrado'     => Ticket::where('status', 'cerrado')->count(),
            ];

            $pending = Ticket::with(['creator', 'assignee'])
                ->whereIn('status', ['abierto', 'en_proceso'])
                ->orderBy('created_at')
                ->limit(10)
                ->get();

            $recent = Ticket::with(['creator', 'assignee'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            return view('dashboards.admin', compact('stats', 'pending', 'recent'));
        }

        if ($user->hasRole('tecnico')) {

            $stats = [
                // Cola = abiertos (sin asignar) GLOBAL
                'cola' => Ticket::where('status', 'abierto')->count(),

                // Atención = en_proceso asignados al técnico
                'atencion' => Ticket::where('assigned_to', auth()->id())
                    ->where('status', 'en_proceso')
                    ->count(),

                // Finalizados = resuelto/cerrado del técnico
                'finalizados' => Ticket::where('assigned_to', auth()->id())
                    ->whereIn('status', ['resuelto', 'cerrado'])
                    ->count(),
            ];

            // Tabla izquierda
            $pending = Ticket::with(['creator.area'])
                ->where('assigned_to', auth()->id())
                ->where('status', 'en_proceso')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Tabla derecha
            $recentFinalized = Ticket::with(['creator.area'])
                ->where('assigned_to', auth()->id())
                ->whereIn('status', ['resuelto', 'cerrado'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            return view('dashboards.tecnico', compact('stats', 'pending', 'recentFinalized'));
        }

        // usuario
        $stats = [
            'total'      => Ticket::where('created_by', auth()->id())->count(),
            'abierto'     => Ticket::where('created_by', auth()->id())->where('status', 'abierto')->count(),
            'en_proceso'  => Ticket::where('created_by', auth()->id())->where('status', 'en_proceso')->count(),
            'resuelto'    => Ticket::where('created_by', auth()->id())->where('status', 'resuelto')->count(),
            'cerrado'     => Ticket::where('created_by', auth()->id())->where('status', 'cerrado')->count(),
        ];

        $latestTickets = \App\Models\Ticket::with('assignee')
            ->where('created_by', auth()->id())
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('dashboards.usuario', compact('stats', 'latestTickets'));
    }
}
