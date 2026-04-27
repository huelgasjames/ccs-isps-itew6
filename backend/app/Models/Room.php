<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'room_code',
        'type',
        'capacity',
        'floor',
        'building',
        'description',
        'status',
        'equipment',
        'hourly_rate',
    ];

    protected $casts = [
        'equipment' => 'array',
        'hourly_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByBuilding($query, $building)
    {
        return $query->where('building', $building);
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        return [
            'lecture' => 'Lecture Hall',
            'lab' => 'Laboratory',
            'computer_lab' => 'Computer Lab',
            'multimedia' => 'Multimedia Room',
            'conference' => 'Conference Room',
        ][$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute()
    {
        return [
            'available' => 'Available',
            'maintenance' => 'Under Maintenance',
            'occupied' => 'Occupied',
            'unavailable' => 'Unavailable',
        ][$this->status] ?? $this->status;
    }

    public function getCapacityUtilizationAttribute()
    {
        $totalCapacity = $this->capacity;
        $scheduledCapacity = $this->schedules()
            ->where('status', 'active')
            ->sum('max_capacity');

        return $totalCapacity > 0 ? ($scheduledCapacity / $totalCapacity) * 100 : 0;
    }
}
