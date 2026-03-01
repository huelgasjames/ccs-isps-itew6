<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $table = 'sports';

    protected $fillable = [
        'student_id',
        'sport_name',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
