<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'start_datetime',
        'end_datetime',
        'venue',
        'organizer',
        'target_audience',
        'target_audience_specification',
        'max_participants',
        'current_participants',
        'registration_fee',
        'status',
        'poster_image',
        'requirements',
        'created_by',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'registration_fee' => 'decimal:2',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming' && $this->start_datetime > now();
    }

    public function isOngoing(): bool
    {
        return $this->status === 'ongoing' || 
               ($this->start_datetime <= now() && $this->end_datetime >= now());
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->end_datetime < now();
    }

    public function hasAvailableSlots(): bool
    {
        return !$this->max_participants || $this->current_participants < $this->max_participants;
    }

    public function getAvailableSlotsAttribute(): ?int
    {
        if (!$this->max_participants) return null;
        return $this->max_participants - $this->current_participants;
    }
}
