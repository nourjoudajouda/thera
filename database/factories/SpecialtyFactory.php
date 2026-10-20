<?php

namespace Database\Factories;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Specialty>
 */
class SpecialtyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'name_ar' => fake()->unique()->words(2, true),
            'name_en' => fake()->unique()->words(2, true),
            'is_active' => true,
        ];
    }

    public function forParent(Specialty $parent): static
    {
        return $this->state(fn (): array => ['parent_id' => $parent->id]);
    }
}
