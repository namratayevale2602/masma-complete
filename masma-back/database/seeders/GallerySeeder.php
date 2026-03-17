<?php
// database/seeders/GallerySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gallery;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $galleries = [
            [
                'title' => 'Expo Brochure was Launched by Shri Atul Save ji, Hon\'ble Minister (Renewable Energy), Govt of Maharastra',
                'featured_image' => 'gallery/expo-brochure.jpg',
                'images' => [
                    'gallery/expo-brochure-1.jpg',
                    'gallery/expo-brochure-2.jpg',
                ],
                'order' => 1,
            ],
            [
                'title' => 'With Mrs Meghana Bordikar Hon\'ble Minister of State (Energy), Govt of Maharastra',
                'featured_image' => 'gallery/meghana-bordikar.jpg',
                'images' => [
                    'gallery/meghana-bordikar-1.jpg',
                    'gallery/meghana-bordikar-2.jpg',
                    'gallery/meghana-bordikar-3.jpg',
                ],
                'order' => 2,
            ],
            [
                'title' => 'Meeting Shri Chandrakant Dada Patil, Hon\'ble Minister (Higher & Technical Education), Govt of Maharashtra',
                'featured_image' => 'gallery/chandrakant-patil.jpg',
                'images' => [
                    'gallery/chandrakant-patil-1.jpg',
                    'gallery/chandrakant-patil-2.jpg',
                ],
                'order' => 3,
            ],
            [
                'title' => 'With Shri Om Prakash Bakoria (IAS), Hon\'ble Director General, Maharashtra Energy Development Agency (MEDA)',
                'featured_image' => 'gallery/om-prakash-bakoria.jpg',
                'images' => [
                    'gallery/om-prakash-bakoria-1.jpg',
                    'gallery/om-prakash-bakoria-2.jpg',
                ],
                'order' => 4,
            ],
            [
                'title' => 'With Shri. Vishwas Pathak, Independent Director, MSEDCL',
                'featured_image' => 'gallery/vishwas-pathak.jpg',
                'images' => [
                    'gallery/vishwas-pathak-1.jpg',
                    'gallery/vishwas-pathak-2.jpg',
                    'gallery/vishwas-pathak-3.jpg',
                    'gallery/vishwas-pathak-4.jpg',
                    'gallery/vishwas-pathak-5.jpg',
                ],
                'order' => 5,
            ],
        ];

        foreach ($galleries as $gallery) {
            Gallery::create($gallery);
        }
    }
}