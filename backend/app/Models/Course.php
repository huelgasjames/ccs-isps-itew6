<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        'description',
        'credits',
        'department',
        'year_level',
        'semester',
        'academic_year',
        'instructor',
        'schedule',
        'room',
        'max_students',
        'current_students',
        'status',
        'prerequisites',
    ];

    protected $casts = [
        'credits' => 'integer',
        'max_students' => 'integer',
        'current_students' => 'integer',
        'prerequisites' => 'array',
    ];

    protected $attributes = [
        'max_students' => 40,
        'current_students' => 0,
        'status' => 'active',
    ];

    public function syllabi(): HasMany
    {
        return $this->hasMany(Syllabus::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function activeSchedules(): HasMany
    {
        return $this->schedules()->where('status', 'active');
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_enrollments')
                   ->withPivot(['grade', 'status', 'semester', 'academic_year', 'enrollment_date'])
                   ->withTimestamps();
    }

    public function getIsFullAttribute(): bool
    {
        return $this->current_students >= $this->max_students;
    }

    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->max_students - $this->current_students);
    }

    public function getEnrollmentPercentageAttribute(): float
    {
        if ($this->max_students === 0) return 0;
        return ($this->current_students / $this->max_students) * 100;
    }

    public function canEnrollStudent(): bool
    {
        return $this->status === 'active' && !$this->is_full && $this->current_students < 40;
    }

    public function getEnrollmentStatusAttribute(): string
    {
        if ($this->status !== 'active') return 'inactive';
        if ($this->is_full) return 'full';
        if ($this->enrollment_percentage >= 80) return 'almost_full';
        return 'available';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('course_code', 'like', "%{$search}%")
              ->orWhere('course_name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('instructor', 'like', "%{$search}%");
        });
    }
}
