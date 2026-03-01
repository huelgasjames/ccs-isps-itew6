<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'certificate_name',
        'date_received',
    ];

    protected $casts = [
        'date_received' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
