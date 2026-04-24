<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\StoredAnnouncement;
use App\Models\AnnouncementAttachment;
use App\Models\AnnouncementComment;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index(Request $request)
    {
        $query = Announcement::with(['user', 'views']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // For public access, only show published announcements
        $query->where('status', 'published');

        // Check if request wants paginated data
        $perPage = $request->get('per_page', 10);
        if ($perPage === 'all') {
            $announcements = $query->orderBy('created_at', 'desc')->get();
        } else {
            $announcements = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);
        }

        return response()->json($announcements);
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request)
    {
        // Validate the new schema fields
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'type' => 'nullable|string|in:System,Academic,Event,Feature,Policy,General',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'required|in:draft,published,scheduled,archived',
            'publish_date' => 'nullable|date',
            'expires_at' => 'nullable|date|after:publish_date',
            'target_type' => 'required|in:all,students,professors,specific',
            'target_users' => 'nullable|array',
            'target_users.*' => 'integer',
            'target_all' => 'nullable|boolean',
            'target_students' => 'nullable|boolean',
            'target_professors' => 'nullable|boolean',
            'target_admins' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Validate image
            $validationErrors = ImageHelper::validateImage($image);
            if (!empty($validationErrors)) {
                return response()->json(['errors' => $validationErrors], 422);
            }
            
            $path = ImageHelper::uploadAnnouncementImage($image);
            $data['image'] = $path;
        }

        // Set default values
        $data['user_id'] = Auth::id();
        $data['type'] = $data['type'] ?? 'General';
        $data['priority'] = $data['priority'] ?? 'medium';
        $data['target_all'] = $data['target_all'] ?? true;
        $data['target_students'] = $data['target_students'] ?? false;
        $data['target_professors'] = $data['target_professors'] ?? false;
        $data['target_admins'] = $data['target_admins'] ?? false;
        
        if (!isset($data['target_users'])) {
            $data['target_users'] = null;
        }

        $announcement = Announcement::create($data);

        return response()->json($announcement, 201);
    }

    /**
     * Display the specified announcement.
     */
    public function show($id)
{
    $announcement = Announcement::with(['user', 'views'])
        ->findOrFail($id);

    return response()->json($announcement);
}

    /**
     * Update the specified announcement.
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'type' => 'required|string|in:System,Academic,Event,Feature,Policy,General',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:draft,published,scheduled,archived',
            'publish_date' => 'nullable|date',
            'expires_at' => 'nullable|date|after:publish_date',
            'target_all' => 'boolean',
            'target_students' => 'boolean',
            'target_professors' => 'boolean',
            'target_admins' => 'boolean',
        ]);

        $announcement->update($request->all());

        return response()->json($announcement->load('attachments'));
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        // Delete attachments
        foreach ($announcement->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }
        
        $announcement->delete();

        return response()->json(null, 204);
    }

    /**
     * Add comment to announcement
     */
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $announcement = Announcement::findOrFail($id);

        $comment = AnnouncementComment::create([
            'announcement_id' => $announcement->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        $announcement->incrementComments();

        return response()->json($comment->load('user'), 201);
    }

    /**
     * Get comments for announcement
     */
    public function getComments($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $comments = $announcement->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($comments);
    }

    /**
     * Delete comment
     */
    public function deleteComment($announcementId, $commentId)
    {
        $comment = AnnouncementComment::findOrFail($commentId);
        
        $user = Auth::user();
        if (!$comment->canDelete($user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(null, 204);
    }

    /**
     * Toggle like on announcement
     */
    public function toggleLike($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        // For now, just increment likes. In a real app, you'd track individual likes
        $announcement->incrementLikes();

        return response()->json(['likes' => $announcement->fresh()->likes]);
    }

    /**
     * Mark announcement as viewed by user
     */
    public function markAsViewed($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $announcement = Announcement::findOrFail($id);
        
        // Mark as viewed
        $announcement->markAsViewedBy($user->id);
        
        return response()->json(['message' => 'Announcement marked as viewed']);
    }

    /**
     * Get recent announcements for dashboard
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 5);
        
        $query = Announcement::published()
            ->orderBy('publish_date', 'desc')
            ->take($limit);

        $user = Auth::user();
        if ($user && $user->role !== 'admin') {
            $query->visibleTo($user);
        }

        $announcements = $query->get(['id', 'title', 'excerpt', 'publish_date', 'priority', 'type']);

        return response()->json($announcements);
    }

    /**
     * Download attachment
     */
    public function downloadAttachment($id)
    {
        $attachment = AnnouncementAttachment::findOrFail($id);
        
        $filePath = storage_path("app/public/{$attachment->file_path}");
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download($filePath, $attachment->original_name);
    }
}
