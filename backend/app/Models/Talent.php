<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talent extends Model
{
    use HasFactory;

    protected $table = 'talents';

    protected $fillable = [
        'student_id',
        'talent_name',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
