<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Room::withCount('schedules');

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->get('type'));
        }

        // Filter by building
        if ($request->has('building')) {
            $query->byBuilding($request->get('building'));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Search by name or room code
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('room_code', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%");
            });
        }

        $rooms = $query->orderBy('building')
                      ->orderBy('floor')
                      ->orderBy('name')
                      ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $rooms->items(),
            'pagination' => [
                'current_page' => $rooms->currentPage(),
                'last_page' => $rooms->lastPage(),
                'per_page' => $rooms->perPage(),
                'total' => $rooms->total(),
                'from' => $rooms->firstItem(),
                'to' => $rooms->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_code' => 'required|string|max:50|unique:rooms',
            'type' => 'required|in:lecture,lab,computer_lab,multimedia,conference',
            'capacity' => 'required|integer|min:1|max:500',
            'floor' => 'nullable|integer|min:1|max:20',
            'building' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:available,maintenance,occupied,unavailable',
            'equipment' => 'nullable|array',
            'equipment.*' => 'string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0|max:10000',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room created successfully',
            'data' => $room
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room): JsonResponse
    {
        $room->load('schedules');

        return response()->json([
            'success' => true,
            'data' => $room
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'room_code' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('rooms')->ignore($room->id)],
            'type' => 'sometimes|required|in:lecture,lab,computer_lab,multimedia,conference',
            'capacity' => 'sometimes|required|integer|min:1|max:500',
            'floor' => 'nullable|integer|min:1|max:20',
            'building' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'status' => 'sometimes|required|in:available,maintenance,occupied,unavailable',
            'equipment' => 'nullable|array',
            'equipment.*' => 'string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0|max:10000',
        ]);

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully',
            'data' => $room
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room): JsonResponse
    {
        // Check if room has active schedules
        $activeSchedules = $room->schedules()->where('status', 'active')->count();
        
        if ($activeSchedules > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete room with active schedules',
                'active_schedules' => $activeSchedules
            ], 422);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully'
        ]);
    }

    /**
     * Get room statistics
     */
    public function statistics(): JsonResponse
    {
        $totalRooms = Room::count();
        $availableRooms = Room::available()->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        
        $roomsByType = Room::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $roomsByBuilding = Room::selectRaw('building, COUNT(*) as count')
            ->whereNotNull('building')
            ->groupBy('building')
            ->pluck('count', 'building')
            ->toArray();

        $totalCapacity = Room::sum('capacity');
        $avgCapacity = Room::avg('capacity');

        return response()->json([
            'success' => true,
            'data' => [
                'total_rooms' => $totalRooms,
                'available_rooms' => $availableRooms,
                'maintenance_rooms' => $maintenanceRooms,
                'occupied_rooms' => $occupiedRooms,
                'rooms_by_type' => $roomsByType,
                'rooms_by_building' => $roomsByBuilding,
                'total_capacity' => $totalCapacity,
                'average_capacity' => round($avgCapacity, 2),
            ]
        ]);
    }

    /**
     * Get available rooms for scheduling
     */
    public function available(Request $request): JsonResponse
    {
        $query = Room::available()
            ->withCount(['schedules' => function ($query) {
                $query->where('status', 'active');
            }]);

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->get('type'));
        }

        // Filter by capacity
        if ($request->has('min_capacity')) {
            $query->where('capacity', '>=', $request->get('min_capacity'));
        }

        if ($request->has('max_capacity')) {
            $query->where('capacity', '<=', $request->get('max_capacity'));
        }

        $rooms = $query->orderBy('building')
                      ->orderBy('floor')
                      ->orderBy('name')
                      ->get();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }
}
