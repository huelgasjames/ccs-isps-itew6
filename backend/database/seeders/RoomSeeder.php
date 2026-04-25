<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['name' => 'Room 101', 'building' => 'Main', 'floor' => 1, 'room_type' => 'lecture', 'capacity' => 40, 'status' => 'active'],
            ['name' => 'Room 102', 'building' => 'Main', 'floor' => 1, 'room_type' => 'lab', 'capacity' => 30, 'status' => 'active'],
            ['name' => 'Room 103', 'building' => 'Main', 'floor' => 1, 'room_type' => 'computer_lab', 'capacity' => 35, 'status' => 'active'],
            ['name' => 'Room 201', 'building' => 'Main', 'floor' => 2, 'room_type' => 'lecture', 'capacity' => 40, 'status' => 'active'],
            ['name' => 'Room 302', 'building' => 'Annex', 'floor' => 3, 'room_type' => 'multimedia', 'capacity' => 45, 'status' => 'active'],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(['name' => $room['name']], $room);
        }
    }
}
