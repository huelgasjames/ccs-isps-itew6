<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoredAnnouncement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'type',
        'priority',
        'status',
        'publish_date',
        'expires_at',
        'target_all',
        'target_students',
        'target_professors',
        'target_admins',
        'author',
        'user_id',
        'author_role',
        'views',
        'likes',
        'comments',
        'storage_path',
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

    /**
     * Get the user who created the announcement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get published announcements.
     */
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

    /**
     * Scope to get announcements visible to a specific user role.
     */
    public function scopeVisibleTo($query, $userRole)
    {
        return $query->where(function ($q) use ($userRole) {
            $q->where('target_all', true)
              ->orWhere("target_{$userRole}", true);
        });
    }

    /**
     * Get formatted priority label.
     */
    public function getPriorityLabelAttribute()
    {
        return ucfirst($this->priority);
    }

    /**
     * Get formatted status label.
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Check if announcement is expired.
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if announcement is scheduled for future.
     */
    public function isScheduled()
    {
        return $this->publish_date && $this->publish_date->isFuture();
    }

    /**
     * Store announcement content to file.
     */
    public function storeToFile()
    {
        $filename = 'announcement_' . $this->id . '_' . time() . '.json';
        $this->storage_path = 'stored_announcements/' . $filename;
        
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'type' => $this->type,
            'priority' => $this->priority,
            'status' => $this->status,
            'publish_date' => $this->publish_date,
            'expires_at' => $this->expires_at,
            'target_all' => $this->target_all,
            'target_students' => $this->target_students,
            'target_professors' => $this->target_professors,
            'target_admins' => $this->target_admins,
            'author' => $this->author,
            'author_role' => $this->author_role,
            'views' => $this->views,
            'likes' => $this->likes,
            'comments' => $this->comments,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        
        $content = json_encode($data, JSON_PRETTY_PRINT);
        \Storage::disk('local')->put($this->storage_path, $content);
        
        $this->save();
    }

    /**
     * Load announcement content from file.
     */
    public static function loadFromFile($filename)
    {
        $path = 'stored_announcements/' . $filename;
        
        if (!\Storage::disk('local')->exists($path)) {
            return null;
        }
        
        $content = \Storage::disk('local')->get($path);
        $data = json_decode($content, true);
        
        return $data;
    }
}
