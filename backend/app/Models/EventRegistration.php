<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'student_id',
        'status',
        'registration_time',
        'notes',
        'payment_status',
        'payment_completed',
    ];

    protected $casts = [
        'registration_time' => 'datetime',
        'payment_status' => 'decimal:2',
        'payment_completed' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function hasPaid(): bool
    {
        return $this->payment_completed || $this->payment_status >= $this->event->registration_fee;
    }
}
