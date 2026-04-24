<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Personal Information
        'student_id',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'age',
        'gender',
        'address',
        'city',
        'province',
        'postal_code',
        
        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        
        // Academic Standing
        'current_year',
        'current_semester',
        'current_gpa',
        'total_units',
        'standing',
        'advisor',
        
        // Legacy fields for compatibility
        'blood_type',
        'disability_status',
        'disability_name',
        'scholar',
        'working_student',
        'contact_number',
        'section',
        'year_level',
        'college',
        'program',
        'curriculum',
        'academic_status',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'disability_status' => 'boolean',
        'scholar' => 'boolean',
        'working_student' => 'boolean',
        'is_active' => 'boolean',
        'current_gpa' => 'decimal:2',
    ];

    protected $appends = ['full_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->hasMany(StudentSkill::class, 'student_id');
    }

    public function activities()
    {
        return $this->hasMany(StudentActivity::class, 'student_id');
    }

    public function academicHistory()
    {
        return $this->hasMany(StudentAcademicHistory::class, 'student_id');
    }

    public function affiliations()
    {
        return $this->hasMany(StudentAffiliation::class, 'student_id');
    }

    public function violations()
    {
        return $this->hasMany(StudentViolation::class, 'student_id');
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                   ->withPivot(['grade', 'status', 'semester', 'academic_year', 'enrollment_date'])
                   ->withTimestamps();
    }

    // Legacy relationships for compatibility
    public function talents()
    {
        return $this->hasMany(Talent::class);
    }

    public function sports()
    {
        return $this->hasMany(Sport::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    public function getPersonalInfoAttribute()
    {
        return [
            'firstName' => $this->first_name,
            'middleName' => $this->middle_name,
            'lastName' => $this->last_name,
            'studentId' => $this->student_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'dateOfBirth' => $this->date_of_birth?->format('Y-m-d'),
            'age' => $this->age,
            'gender' => $this->gender,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'postalCode' => $this->postal_code,
            'emergencyContact' => [
                'name' => $this->emergency_contact_name,
                'relationship' => $this->emergency_contact_relationship,
                'phone' => $this->emergency_contact_phone,
            ]
        ];
    }

    public function getAcademicStandingAttribute()
    {
        return [
            'currentYear' => $this->current_year,
            'currentSemester' => $this->current_semester,
            'currentGPA' => (float) $this->current_gpa,
            'totalUnits' => $this->total_units,
            'standing' => $this->standing,
            'advisor' => $this->advisor,
        ];
    }

    public function isAtRisk()
    {
        return $this->violations()->where('status', 'pending')->count() >= 3;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
