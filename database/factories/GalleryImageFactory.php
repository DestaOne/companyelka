<?php

namespace Database\Factories;

use App\Models\GalleryImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryImage>
 */
class GalleryImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=2400',
            'thumbnail' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1000',
        ];
    }
}
