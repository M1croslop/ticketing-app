<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use App\Models\TicketStatusLog;


class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    const PRIORITIES = ['low', 'medium', 'high', 'urgent'];
    const STATUSES   = ['open', 'in_progress', 'resolved', 'closed'];

    /**
     * Mapa SLA: prioridad → tiempo límite en horas.
     * Se usa al crear el ticket para calcular due_date automáticamente.
     */
    private const SLA_HOURS = [
        'urgent' => 4,
        'high'   => 24,
        'medium' => 72,
        'low'    => 168,
    ];

    /**
     * Boot del modelo.
     *
     * Reglas SLA:
     *  - creating : solo calcula due_date si el ticket ya viene con agente
     *               y aún no tiene due_date manual.
     *  - updating : calcula due_date la primera vez que se asigna un agente
     *               (agent_id pasó de null → valor) y due_date sigue vacío.
     */
    protected static function booted(): void
    {
        // Al CREAR: solo si viene con agente asignado desde el inicio
        static::creating(function (Ticket $ticket): void {
            if ($ticket->agent_id && ! $ticket->due_date) {
                $ticket->due_date = self::calculateDueDate($ticket->priority);
            }
        });

        // Al ACTUALIZAR: primera asignación de agente (null → id)
        static::updating(function (Ticket $ticket): void {
            $agentJustAssigned = $ticket->isDirty('agent_id')
                && $ticket->getOriginal('agent_id') === null
                && $ticket->agent_id !== null;

            if ($agentJustAssigned && ! $ticket->due_date) {
                $ticket->due_date = self::calculateDueDate($ticket->priority);
            }

            // cambio de status a resolved/closed, guardar fecha de resolución
            if (
                $ticket->isDirty('status') &&
                in_array($ticket->status, ['resolved', 'closed']) &&
                is_null($ticket->resolved_at)
            ) {
                $ticket->resolved_at = now();
            }
        });

        static::updated(function (Ticket $ticket): void {
            if ($ticket->wasChanged('status')) {
                TicketStatusLog::create([
                    'ticket_id'  => $ticket->id,
                    'changed_by' => auth()->id() ?? $ticket->agent_id ?? $ticket->user_id,
                    'old_status' => $ticket->getOriginal('status'),
                    'new_status' => $ticket->status,
                    'changed_at' => now(),
                ]);
            }
        });
    }

    /**
     * Calcula el due_date a partir de la prioridad del ticket.
     * Método privado reutilizado por los dos listeners del booted().
     */
    private static function calculateDueDate(string $priority): Carbon
    {
        $hours = self::SLA_HOURS[$priority] ?? 72;

        return Carbon::now()->addHours($hours);
    }


    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'agent_id',
        'category_id',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(TicketStatusLog::class);
    }

    public function canBeEditedBy(User $user): bool
    {
        return $user->role === 'admin'
            || ($user->role === 'agent' && $this->agent_id === $user->id);
    }

    /**
     * Calcula el porcentaje de tickets resueltos dentro del SLA (resolved_at <= due_date).
     * Centraliza esta lógica para evitar duplicación en AdminController y DashboardController.
     */
    public static function slaComplianceRate(): float
    {
        $resolved = self::whereIn('status', ['resolved', 'closed'])->count();

        if ($resolved === 0) {
            return 0.0;
        }

        $onTime = self::whereNotNull('resolved_at')
            ->whereNotNull('due_date')
            ->whereColumn('resolved_at', '<=', 'due_date')
            ->count();

        return round(($onTime / $resolved) * 100, 1);
    }
}