<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\PetCategory;
use Illuminate\Database\Seeder;

class PetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Dog', 'icon' => '🐕',
                'breeds' => ['Aspin (Asong Pinoy)', 'Labrador Retriever', 'Golden Retriever',
                             'German Shepherd', 'Shih Tzu', 'Chihuahua', 'Poodle', 'Beagle'],
            ],
            [
                'name' => 'Cat', 'icon' => '🐈',
                'breeds' => ['Puspin (Pusa Pinoy)', 'Persian', 'Siamese', 'Maine Coon',
                             'British Shorthair', 'Ragdoll'],
            ],
            [
                'name' => 'Bird', 'icon' => '🦜',
                'breeds' => ['Parakeet', 'Cockatiel', 'Love Bird', 'Pigeon', 'Canary'],
            ],
            [
                'name' => 'Rabbit', 'icon' => '🐇',
                'breeds' => ['Dutch Rabbit', 'Holland Lop', 'Lionhead', 'Rex'],
            ],
            [
                'name' => 'Small Animal', 'icon' => '🐹',
                'breeds' => ['Hamster', 'Guinea Pig', 'Gerbil', 'Hedgehog'],
            ],
        ];

        foreach ($categories as $catData) {
            $breeds = $catData['breeds'];
            unset($catData['breeds']);

            $category = PetCategory::updateOrCreate(['name' => $catData['name']], $catData);

            foreach ($breeds as $breedName) {
                Breed::updateOrCreate(
                    ['name' => $breedName, 'pet_category_id' => $category->id],
                    ['is_active' => true]
                );
            }
        }
    }
}