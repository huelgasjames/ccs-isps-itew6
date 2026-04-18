<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSkill extends Model
{
    use HasFactory;

    protected $table = 'student_skills';

    protected $fillable = [
        'student_id',
        'name',
        'category',
        'proficiency',
        'years_experience',
        'certifications',
        'last_used',
    ];

    protected $casts = [
        'years_experience' => 'integer',
        'last_used' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
