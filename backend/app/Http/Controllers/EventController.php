<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['createdBy']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('venue', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->target_audience) {
            $query->where('target_audience', $request->target_audience);
        }

        $events = $query->orderBy('start_datetime')->get();

        return response()->json($events);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:curricular,extra_curricular,academic,sports,cultural,seminar,workshop',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'venue' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'target_audience' => 'required|in:all_students,specific_year,specific_course,faculty,all',
            'target_audience_specification' => 'nullable|string',
            'max_participants' => 'nullable|integer|min:1',
            'registration_fee' => 'required|decimal:0,2|min:0',
            'poster_image' => 'nullable|string',
            'requirements' => 'nullable|string',
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['current_participants'] = 0;

        $event = Event::create($validated);

        return response()->json($event, 201);
    }

    public function show(Event $event): JsonResponse
    {
        $event->load(['createdBy', 'registrations.student']);
        return response()->json($event);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:curricular,extra_curricular,academic,sports,cultural,seminar,workshop',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'venue' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'target_audience' => 'required|in:all_students,specific_year,specific_course,faculty,all',
            'target_audience_specification' => 'nullable|string',
            'max_participants' => 'nullable|integer|min:1',
            'registration_fee' => 'required|decimal:0,2|min:0',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'poster_image' => 'nullable|string',
            'requirements' => 'nullable|string',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();
        return response()->json(null, 204);
    }

    public function register(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();
        
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Only students can register for events'], 403);
        }

        $student = $user->student;

        if ($event->current_participants >= $event->max_participants) {
            return response()->json(['message' => 'Event is full'], 400);
        }

        $existingRegistration = EventRegistration::where('event_id', $event->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingRegistration) {
            return response()->json(['message' => 'Already registered for this event'], 400);
        }

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'student_id' => $student->id,
            'status' => 'registered',
            'registration_time' => now(),
            'payment_status' => 0,
            'payment_completed' => $event->registration_fee == 0,
        ]);

        $event->increment('current_participants');

        return response()->json($registration, 201);
    }

    public function unregister(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();
        
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Only students can unregister from events'], 403);
        }

        $student = $user->student;

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$registration) {
            return response()->json(['message' => 'Not registered for this event'], 404);
        }

        $registration->delete();
        $event->decrement('current_participants');

        return response()->json(['message' => 'Successfully unregistered']);
    }

    public function myRegistrations(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Only students can view registrations'], 403);
        }

        $registrations = EventRegistration::with(['event'])
            ->where('student_id', $user->student->id)
            ->get();

        return response()->json($registrations);
    }
}
