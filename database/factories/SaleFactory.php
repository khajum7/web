<?php

namespace Database\Factories;

use App\Constant\StatusConstant;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'delivery_date' => $this->faker->date(),
            'program_start_date' => $this->faker->date(),
            'organization_name' => $this->faker->company(),
            'status' => $this->faker->randomElement(array_values(StatusConstant::$orderStatus)),
            'approval_status' => $this->faker->randomElement(array_values(StatusConstant::$approvedStatus)),
            'created_by' => User::factory()->create()->id,
            'updated_by' => User::factory()->create()->id,
        ];
    }
}
