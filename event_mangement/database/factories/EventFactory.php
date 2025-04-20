<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->unique()->sentence(3) ,
            'description' => fake()->text ,
            'start_time' => fake()->dateTimeBetween('+1 days' , '+1 month') , // 'now' -> '+10 days'
            'end_time' => fake()->dateTimeBetween('+1 month' , '+2 months') ,
        ];
    }
}
