<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
>>>>>>> 25048deddd9a824e336580a4aceee5bd8dd08608
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
<<<<<<< HEAD
    use HasFactory;

    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'type',
        'priority',
        'status',
        'author',
        'publish_date',
        'expires_at',
        'views',
        'likes',
        'comments',
        'target_all',
        'target_students',
        'target_professors',
        'target_admins',
        'user_id',
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'expires_at' => 'datetime',
        'target_all' => 'boolean',
        'target_students' => 'boolean',
        'target_professors' => 'boolean',
        'target_admins' => 'boolean',
        'views' => 'integer',
        'likes' => 'integer',
        'comments' => 'integer',
    ];

    protected $dates = [
        'publish_date',
        'expires_at',
=======
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
>>>>>>> 25048deddd9a824e336580a4aceee5bd8dd08608
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

<<<<<<< HEAD
    public function attachments()
    {
        return $this->hasMany(AnnouncementAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(AnnouncementComment::class);
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isExpired()
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled' && $this->publish_date && now()->isBefore($this->publish_date);
    }

    public function getPriorityColorAttribute()
    {
        return [
            'low' => 'green',
            'medium' => 'blue',
            'high' => 'orange',
            'urgent' => 'red',
       ][$this->priority] ?? 'gray';
    }

    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'gray',
            'published' => 'green',
            'scheduled' => 'orange',
            'archived' => 'gray',
       ][$this->status] ?? 'gray';
    }

    public function getFormattedPublishDateAttribute()
    {
        return $this->publish_date ? $this->publish_date->format('M d, Y H:i') : null;
    }

    public function getFormattedExpiryDateAttribute()
    {
        return $this->expires_at ? $this->expires_at->format('M d, Y H:i') : null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeVisibleTo($query, $user)
    {
        if (!$user) {
            return $query->where('status', 'published');
        }

        if ($user->role === 'admin') {
            return $query;
        }

        $query->where('status', 'published');

        if ($user->role === 'student') {
            $query->where(function ($q) {
                $q->where('target_all', true)
                  ->orWhere('target_students', true);
            });
        } elseif ($user->role === 'professor') {
            $query->where(function ($q) {
                $q->where('target_all', true)
                  ->orWhere('target_professors', true);
            });
        }

        return $query;
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
        $this->increment('comments');
=======
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
>>>>>>> 25048deddd9a824e336580a4aceee5bd8dd08608
    }
}
