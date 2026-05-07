<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function download(TicketAttachment $attachment)
    {
        // Seguridad: solo creador del ticket, técnico asignado o admin
        $user = auth()->user();
        $ticket = $attachment->ticket()->with('assignee')->firstOrFail();

        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assigned_to && (int) $ticket->assigned_to === (int) $user->id;
        $isAdmin = $user->hasRole('admin');
        $isTech = $user->hasRole('tecnico');

        abort_unless($isCreator || $isAssignee || $isAdmin || $isTech, 403);

        abort_unless(Storage::disk('local')->exists($attachment->path), 404);

        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }

    public function view(TicketAttachment $attachment)
    {
        $user = auth()->user();
        $ticket = $attachment->ticket()->with('assignee')->firstOrFail();

        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAdmin = $user->hasRole('admin');
        $isTech = $user->hasRole('tecnico');

        abort_unless($isCreator || $isAdmin || $isTech, 403);

        // Solo permitir vista previa para imágenes
        $mime = (string) ($attachment->mime ?? '');
        abort_unless(str_starts_with($mime, 'image/'), 404);

        abort_unless(\Illuminate\Support\Facades\Storage::disk('local')->exists($attachment->path), 404);

        return \Illuminate\Support\Facades\Storage::disk('local')->response($attachment->path);
    }

    public function destroy(TicketAttachment $attachment)
    {
        $user = auth()->user();
        $ticket = $attachment->ticket()->firstOrFail();

        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAdmin = $user->hasRole('admin');
        $isTech = $user->hasRole('tecnico');

        // Regla: creador puede borrar mientras el ticket NO esté finalizado; técnico/admin siempre
        $isFinal = in_array($ticket->status, ['resuelto', 'cerrado'], true);

        abort_unless($isAdmin || $isTech || ($isCreator && !$isFinal), 403);

        // Borra archivo físico si existe
        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        if ($disk->exists($attachment->path)) {
            $disk->delete($attachment->path);
        }

        $attachment->delete();

        return back()->with('ok', 'Adjunto eliminado.');
    }
}
