<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'room_code' => strtoupper($this->faker->bothify($this->faker->lexify('???'))),
            'type' => $this->faker->randomElement(['lecture', 'lab', 'computer_lab', 'multimedia', 'conference']),
            'capacity' => $this->faker->numberBetween(20, 200),
            'floor' => $this->faker->numberBetween(1, 5),
            'building' => $this->faker->randomElement(['Main Building', 'Science Building', 'Engineering Building', 'Library Building']),
            'description' => $this->faker->sentence(10),
            'status' => $this->faker->randomElement(['available', 'maintenance', 'occupied', 'unavailable']),
            'equipment' => $this->faker->randomElements([
                'Projector', 'Whiteboard', 'Computer', 'Sound System', 
                'Air Conditioning', 'WiFi', 'Smart Board', 'Video Conference'
            ], $this->faker->numberBetween(1, 4)),
            'hourly_rate' => $this->faker->randomFloat(50, 500, 2),
        ];
    }

    /**
     * Create a room with specific type
     */
    public function lectureHall()
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'type' => 'lecture',
            'capacity' => $this->faker->numberBetween(50, 200),
        ]));
    }

    /**
     * Create a computer lab
     */
    public function computerLab()
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'type' => 'computer_lab',
            'capacity' => $this->faker->numberBetween(20, 50),
            'equipment' => ['Computer', 'Projector', 'Air Conditioning', 'WiFi'],
        ]));
    }

    /**
     * Create a laboratory
     */
    public function laboratory()
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'type' => 'lab',
            'capacity' => $this->faker->numberBetween(15, 40),
            'equipment' => ['Lab Equipment', 'Safety Equipment', 'Ventilation'],
        ]));
    }

    /**
     * Create a multimedia room
     */
    public function multimediaRoom()
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'type' => 'multimedia',
            'capacity' => $this->faker->numberBetween(10, 30),
            'equipment' => ['Projector', 'Sound System', 'Video Conference Equipment'],
        ]));
    }

    /**
     * Create an available room
     */
    public function available()
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'status' => 'available',
        ]));
    }
}
