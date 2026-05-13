<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketStatusLog extends Model
{
    protected $fillable = [
        'ticket_id',
        'changed_by',
        'old_status',
        'new_status',
        'changed_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
