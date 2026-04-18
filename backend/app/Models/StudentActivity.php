<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentActivity extends Model
{
    use HasFactory;

    protected $table = 'student_activities';

    protected $fillable = [
        'student_id',
        'name',
        'type',
        'role',
        'start_date',
        'end_date',
        'description',
        'achievements',
        'level',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'achievements' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
