<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'image',
        'type',
        'priority',
        'status',
        'publish_date',
        'expires_at',
        'user_id',
        'target_users',
        'target_type',
        'target_all',
        'target_students',
        'target_professors',
        'target_admins',
        'views',
        'likes',
        'comments_count',
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

    public function attachments()
    {
        return $this->hasMany(AnnouncementAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(AnnouncementComment::class);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function incrementLikes()
    {
        $this->increment('likes');
    }

    public function incrementComments()
    {
        $this->increment('comments_count');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where(function ($q) {
                        $q->whereNull('publish_date')
                          ->orWhere('publish_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeVisibleTo($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('target_all', true)
              ->orWhere('target_students', $user->role === 'student')
              ->orWhere('target_professors', $user->role === 'professor')
              ->orWhere('target_admins', $user->role === 'admin')
              ->orWhere(function ($subQ) use ($user) {
                  $subQ->where('target_type', 'specific')
                        ->whereJsonContains('target_users', $user->id);
              });
        });
    }
}
