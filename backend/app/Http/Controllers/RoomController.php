<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Room::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('building', 'like', "%{$search}%")
                    ->orWhere('room_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_type')) {
            $query->where('room_type', $request->room_type);
        }

        return response()->json($query->orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:rooms,name',
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|integer|min:0|max:50',
            'room_type' => 'required|in:lecture,lab,computer_lab,multimedia,other',
            'capacity' => 'required|integer|min:1|max:300',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
        ]);

        return response()->json(Room::create($validated), 201);
    }

    public function show(Room $room): JsonResponse
    {
        return response()->json($room);
    }

    public function update(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:rooms,name,' . $room->id,
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|integer|min:0|max:50',
            'room_type' => 'required|in:lecture,lab,computer_lab,multimedia,other',
            'capacity' => 'required|integer|min:1|max:300',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
        ]);

        $room->update($validated);
        return response()->json($room);
    }

    public function destroy(Room $room): JsonResponse
    {
        $room->delete();
        return response()->json(null, 204);
    }
}
