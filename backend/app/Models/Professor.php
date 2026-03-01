<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $fillable = [
        'professor_unique_id',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'age',
        'birthday',
        'blood_type',
        'disability_status',
        'gender',
        'email',
        'contact_number',
        'address',
        'educational_attainment',
        'experience',
        'courses_handled',
        'role',
        'employment_type',
        'department',
        'organization',
        'application_date',
    ];

    protected $casts = [
        'disability_status' => 'boolean',
        'birthday' => 'date',
        'application_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }
}
