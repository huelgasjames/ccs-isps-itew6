<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lecture Halls
        Room::factory()->lectureHall()->count(8)->create([
            'name' => 'Main Lecture Hall',
            'room_code' => 'LH-001',
            'building' => 'Main Building',
            'floor' => 1,
            'status' => 'available',
        ]);

        Room::factory()->lectureHall()->count(5)->create([
            'building' => 'Main Building',
            'floor' => 2,
        ]);

        Room::factory()->lectureHall()->count(3)->create([
            'building' => 'Science Building',
            'floor' => 1,
        ]);

        // Computer Labs
        Room::factory()->computerLab()->count(6)->create([
            'name' => 'Computer Lab A',
            'room_code' => 'CL-001',
            'building' => 'Main Building',
            'floor' => 3,
            'status' => 'available',
        ]);

        Room::factory()->computerLab()->count(4)->create([
            'building' => 'Engineering Building',
            'floor' => 2,
        ]);

        // Laboratories
        Room::factory()->laboratory()->count(5)->create([
            'name' => 'Chemistry Lab',
            'room_code' => 'LAB-001',
            'building' => 'Science Building',
            'floor' => 2,
            'status' => 'available',
        ]);

        Room::factory()->laboratory()->count(3)->create([
            'building' => 'Science Building',
            'floor' => 3,
        ]);

        // Multimedia Rooms
        Room::factory()->multimediaRoom()->count(4)->create([
            'name' => 'Multimedia Room 1',
            'room_code' => 'MM-001',
            'building' => 'Main Building',
            'floor' => 4,
            'status' => 'available',
        ]);

        Room::factory()->multimediaRoom()->count(2)->create([
            'building' => 'Library Building',
            'floor' => 2,
        ]);

        // Conference Rooms
        Room::factory()->create([
            'name' => 'Conference Room A',
            'room_code' => 'CR-001',
            'type' => 'conference',
            'capacity' => 20,
            'building' => 'Main Building',
            'floor' => 5,
            'status' => 'available',
            'equipment' => ['Projector', 'Video Conference Equipment', 'Sound System', 'Whiteboard'],
            'hourly_rate' => 150.00,
        ]);

        Room::factory()->create([
            'name' => 'Executive Conference Room',
            'room_code' => 'CR-002',
            'type' => 'conference',
            'capacity' => 15,
            'building' => 'Main Building',
            'floor' => 5,
            'status' => 'maintenance',
            'equipment' => ['Projector', 'Video Conference Equipment', 'Sound System'],
            'hourly_rate' => 200.00,
        ]);

        // Add some rooms with different statuses
        Room::factory()->count(3)->create([
            'status' => 'maintenance',
            'building' => 'Engineering Building',
        ]);

        Room::factory()->count(2)->create([
            'status' => 'occupied',
            'building' => 'Main Building',
        ]);

        Room::factory()->count(1)->create([
            'status' => 'unavailable',
            'building' => 'Science Building',
        ]);
    }
}
