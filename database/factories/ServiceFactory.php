<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => ucfirst($title),
            'slug' => str($title)->slug()->toString(),
            'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c',
            'summary' => fake()->sentence(),
            'detail' => "# Cakupan\n- ".fake()->words(2, true)."\n".fake()->sentence(),
            'status' => Service::STATUS_PUBLISHED,
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => Service::STATUS_DRAFT]);
    }
}
