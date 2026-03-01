<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'violation_type',
        'description',
        'sanction',
        'date_committed',
        'status',
    ];

    protected $casts = [
        'date_committed' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
