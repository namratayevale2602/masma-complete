<?php
// database/seeders/SocialMediaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocialMedia;

class SocialMediaSeeder extends Seeder
{
    public function run(): void
    {
        $socialMedia = [
            [
                'platform' => 'Facebook',
                'icon' => 'FaFacebook',
                'url' => '#',
                'color' => 'hover:bg-blue-600',
                'order' => 1,
            ],
            [
                'platform' => 'Twitter',
                'icon' => 'FaTwitter',
                'url' => '#',
                'color' => 'hover:bg-sky-500',
                'order' => 2,
            ],
            [
                'platform' => 'LinkedIn',
                'icon' => 'FaLinkedin',
                'url' => '#',
                'color' => 'hover:bg-blue-700',
                'order' => 3,
            ],
            [
                'platform' => 'Instagram',
                'icon' => 'FaInstagram',
                'url' => '#',
                'color' => 'hover:bg-pink-600',
                'order' => 4,
            ],
        ];

        foreach ($socialMedia as $social) {
            SocialMedia::create($social);
        }
    }
}