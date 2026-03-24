<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'user_id',
        'status',
        'target_users',
        'target_type',
    ];

    protected $casts = [
        'target_users' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(AnnouncementView::class);
    }

    public function viewedByUsers()
    {
        return $this->belongsToMany(User::class, 'announcement_views')
            ->withPivot('viewed_at')
            ->withTimestamps();
    }

    public function isViewedByUser($userId)
    {
        return $this->views()->where('user_id', $userId)->exists();
    }

    public function markAsViewedBy($userId)
    {
        if (!$this->isViewedByUser($userId)) {
            $this->views()->create(['user_id' => $userId]);
        }
    }

    public function canBeViewedBy($user)
    {
        if ($this->target_type === 'all') {
            return true;
        }

        if ($this->target_type === 'students' && $user->role === 'student') {
            return true;
        }

        if ($this->target_type === 'professors' && $user->role === 'professor') {
            return true;
        }

        if ($this->target_type === 'specific' && $this->target_users) {
            return in_array($user->id, $this->target_users);
        }

        return false;
    }

    public function getViewCount()
    {
        return $this->views()->count();
    }
}
