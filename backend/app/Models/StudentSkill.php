<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'category',
        'proficiency',
        'certifications',
        'years_experience',
        'last_used',
    ];

    protected $casts = [
        'certifications' => 'array',
        'last_used' => 'date',
        'years_experience' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
