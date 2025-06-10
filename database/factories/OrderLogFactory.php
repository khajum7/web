<?php

namespace Database\Factories;

use App\Models\OrderLog;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderLogFactory  extends Factory
{
    protected $model = OrderLog::class;

    public function definition()
    {
        return [
            'comment' => $this->faker->text(),
            'sale_id' => Sale::factory()->create()->id,
            'created_by' => User::factory()->create()->id,
            'updated_by' => User::factory()->create()->id,
        ];
    }
}
