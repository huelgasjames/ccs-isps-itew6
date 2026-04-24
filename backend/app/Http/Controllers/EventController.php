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

    public function generateSampleData(): JsonResponse
    {
        $samples = [
            [
                'title' => 'CCS Week 2025',
                'description' => 'Annual celebration of the College of Computer Studies featuring various activities, competitions, and showcases of student projects and achievements throughout the week.',
                'type' => 'cultural',
                'status' => 'upcoming',
                'start_datetime' => now()->addDays(7)->setHour(9)->setMinute(0),
                'end_datetime' => now()->addDays(12)->setHour(17)->setMinute(0),
                'venue' => 'CCS Building Auditorium',
                'organizer' => 'CCS Student Council',
                'target_audience' => 'all_students',
                'max_participants' => 200,
                'current_participants' => 45,
                'registration_fee' => 0.00,
                'requirements' => 'Must be a CCS student',
                'created_by' => 1,
            ],
            [
                'title' => 'Web Development Workshop',
                'description' => 'Hands-on workshop covering modern web development technologies including Vue.js, React, and Node.js. Participants will build a full-stack application.',
                'type' => 'workshop',
                'status' => 'upcoming',
                'start_datetime' => now()->addDays(3)->setHour(13)->setMinute(0),
                'end_datetime' => now()->addDays(3)->setHour(17)->setMinute(0),
                'venue' => 'Computer Lab 3',
                'organizer' => 'IT Department',
                'target_audience' => 'specific_course',
                'target_audience_specification' => 'IT and CS Students',
                'max_participants' => 40,
                'current_participants' => 28,
                'registration_fee' => 50.00,
                'requirements' => 'Laptop required',
                'created_by' => 1,
            ],
            [
                'title' => 'Programming Competition',
                'description' => 'Inter-department programming competition. Teams of 3 will solve algorithmic problems within a time limit using C++, Java, or Python.',
                'type' => 'academic',
                'status' => 'upcoming',
                'start_datetime' => now()->addDays(14)->setHour(8)->setMinute(0),
                'end_datetime' => now()->addDays(14)->setHour(17)->setMinute(0),
                'venue' => 'Computer Lab 1 & 2',
                'organizer' => 'ACM Student Chapter',
                'target_audience' => 'all_students',
                'max_participants' => 60,
                'current_participants' => 36,
                'registration_fee' => 100.00,
                'requirements' => 'Team of 3 members',
                'created_by' => 1,
            ],
            [
                'title' => 'Career Seminar: Tech Industry Trends',
                'description' => 'Industry professionals share insights on current trends in the tech industry, career opportunities, and skills in demand for IT graduates.',
                'type' => 'seminar',
                'status' => 'ongoing',
                'start_datetime' => now()->subDays(1)->setHour(9)->setMinute(0),
                'end_datetime' => now()->addDays(1)->setHour(12)->setMinute(0),
                'venue' => 'Main Conference Hall',
                'organizer' => 'Placement Office',
                'target_audience' => 'all',
                'max_participants' => 150,
                'current_participants' => 120,
                'registration_fee' => 0.00,
                'requirements' => 'None',
                'created_by' => 1,
            ],
            [
                'title' => 'Intramural Sports Festival',
                'description' => 'Annual sports festival featuring basketball, volleyball, badminton, and table tennis competitions between departments.',
                'type' => 'sports',
                'status' => 'ongoing',
                'start_datetime' => now()->subDays(3)->setHour(8)->setMinute(0),
                'end_datetime' => now()->addDays(4)->setHour(17)->setMinute(0),
                'venue' => 'University Gymnasium',
                'organizer' => 'Sports Committee',
                'target_audience' => 'all_students',
                'max_participants' => 300,
                'current_participants' => 185,
                'registration_fee' => 0.00,
                'requirements' => 'Must be enrolled for current semester',
                'created_by' => 1,
            ],
            [
                'title' => 'Hackathon 2024',
                'description' => '24-hour hackathon where teams develop innovative solutions for real-world problems. Prizes for top 3 teams.',
                'type' => 'extra_curricular',
                'status' => 'completed',
                'start_datetime' => now()->subDays(30)->setHour(8)->setMinute(0),
                'end_datetime' => now()->subDays(29)->setHour(8)->setMinute(0),
                'venue' => 'CCS Building',
                'organizer' => 'Google Developer Student Club',
                'target_audience' => 'specific_course',
                'target_audience_specification' => 'CS and IT Students',
                'max_participants' => 80,
                'current_participants' => 72,
                'registration_fee' => 150.00,
                'requirements' => 'Team of 2-4 members',
                'created_by' => 1,
            ],
        ];

        $created = [];
        foreach ($samples as $data) {
            $existing = Event::where('title', $data['title'])->first();
            if (!$existing) {
                $created[] = Event::create($data);
            }
        }

        return response()->json([
            'message' => count($created) . ' sample events generated',
            'events' => $created,
        ]);
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
