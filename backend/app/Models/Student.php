<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_unique_id',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'age',
        'blood_type',
        'disability_status',
        'disability_name',
        'scholar',
        'working_student',
        'email',
        'contact_number',
        'address',
        'section',
        'year_level',
        'college',
        'program',
        'curriculum',
        'academic_status',
    ];

    protected $casts = [
        'disability_status' => 'boolean',
        'scholar' => 'boolean',
        'working_student' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function talents()
    {
        return $this->hasMany(Talent::class);
    }

    public function sports()
    {
        return $this->hasMany(Sport::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    public function isAtRisk()
    {
        return $this->violations()->where('status', 'Pending')->count() >= 3;
    }
}
