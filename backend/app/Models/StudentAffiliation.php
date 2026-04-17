<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAffiliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'type',
        'role',
        'start_date',
        'position',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
