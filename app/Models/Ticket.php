<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'assigned_to',
        'taken_at',
        'subject',
        'description',
        'category',
        'status',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'taken_at'   => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\TicketComment::class);
    }

    public function ageSemaphore(): array
    {
        $now = now();

        // Finalizados: mostrar duración hasta resolver (no cola/proceso)
        if (in_array($this->status, ['resuelto', 'cerrado'], true)) {
            $start = $this->taken_at ?? $this->created_at;
            $end   = $this->resolved_at ?? $this->closed_at ?? $now;

            $minutes = ($start && $end) ? (int) $start->diffInMinutes($end) : 0;

            $greenMax = config('ticket_age.resolved_green_max');
            $amberMax = config('ticket_age.resolved_amber_max');

            if ($minutes <= $greenMax) {
                $color = 'green';
            } elseif ($minutes <= $amberMax) {
                $color = 'amber';
            } else {
                $color = 'red';
            }

            return [
                'minutes' => $minutes,
                'color' => $color,
            ];
        }

        $start = $this->status === 'en_proceso'
            ? ($this->taken_at ?? $this->created_at)
            : $this->created_at;

        $minutes = $start ? (int) $start->diffInMinutes($now) : 0;

        $greenMax = config('ticket_age.green_max');
        $amberMax = config('ticket_age.amber_max');

        if ($minutes <= $greenMax) {
            $color = 'green';
        } elseif ($minutes <= $amberMax) {
            $color = 'amber';
        } else {
            $color = 'red';
        }

        return [
            'minutes' => $minutes,
            'color' => $color,
        ];
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(\App\Models\TicketAttachment::class);
    }

    public static function statusLabel(?string $status): ?string
    {
        return match ($status) {
            'abierto' => 'Abierto',
            'en_proceso' => 'En atención',
            'resuelto' => 'Resuelto',
            'cerrado' => 'Cerrado',
            'finalizados' => 'Resueltos / Cerrados',
            default => null,
        };
    }
}
