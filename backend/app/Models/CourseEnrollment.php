<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'schedule_id',
        'semester',
        'academic_year',
        'grade',
        'status',
        'enrollment_date',
        'remarks',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'enrollment_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function scopeForSemester($query, $semester, $academicYear)
    {
        return $query->where('semester', $semester)
                   ->where('academic_year', $academicYear);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isPassed(): bool
    {
        return $this->grade !== null && $this->grade >= 75;
    }

    public function isFailed(): bool
    {
        return $this->grade !== null && $this->grade < 75;
    }
}
