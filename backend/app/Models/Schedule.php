<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'professor_id',
        'section',
        'room',
        'room_type',
        'day_of_week',
        'start_time',
        'end_time',
        'academic_year',
        'semester',
        'max_capacity',
        'current_enrollment',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function getAvailableSlotsAttribute(): int
    {
        return $this->max_capacity - $this->current_enrollment;
    }

    public function isFull(): bool
    {
        return $this->current_enrollment >= $this->max_capacity;
    }
}
