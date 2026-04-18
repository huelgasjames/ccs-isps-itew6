<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAcademicHistory extends Model
{
    use HasFactory;

    protected $table = 'student_academic_history';

    protected $fillable = [
        'student_id',
        'school_name',
        'degree',
        'major',
        'start_date',
        'end_date',
        'gpa',
        'honors',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'gpa' => 'decimal:2',
        'honors' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
