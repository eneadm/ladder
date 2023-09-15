<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ladder\Models\ModelRole;

class ModelRoleFactory extends Factory
{
    protected $model = ModelRole::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => $this->faker->word(),
        ];
    }
}
