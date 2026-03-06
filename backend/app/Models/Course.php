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
        'level',
        'semester',
        'prerequisites',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
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
}
