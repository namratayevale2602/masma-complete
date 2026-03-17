<?php
// database/seeders/RegionalDirectorSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegionalDirector;

class RegionalDirectorSeeder extends Seeder
{
    public function run(): void
    {
        $directors = [
            // Regional Directors
            [
                'category_title' => 'Regional Director',
                'category_icon' => 'FaUserTie',
                'category_order' => 1,
                'members' => [
                    [
                        'name' => 'Mr. Abhijit Vichare',
                        'city' => 'Kolhapur',
                        'region' => 'West Maharashtra',
                        'image' => 'regional-directors/abhijit-vichare.jpg',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Mr. Arun Singavi',
                        'city' => 'Nashik',
                        'region' => 'North Maharashtra',
                        'image' => 'regional-directors/arun-singavi.jpg',
                        'order' => 2,
                    ],
                ],
            ],
            
            // District Directors
            [
                'category_title' => 'District Director',
                'category_icon' => 'FaCrown',
                'category_order' => 2,
                'members' => [
                    [
                        'name' => 'Mr. Omkar Korgaonkar',
                        'city' => 'Vengurla',
                        'region' => 'Sindhudurg',
                        'image' => 'regional-directors/omkar-korgaonkar.jpg',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Mr. Subhash Chandane',
                        'city' => 'Ch.SambhajiNagar',
                        'region' => 'Ch.SambhajiNagar',
                        'image' => 'regional-directors/subhash-chandane.jpg',
                        'order' => 2,
                    ],
                    [
                        'name' => 'Mr. Sushil Petkar',
                        'city' => 'Ratnagiri',
                        'region' => 'Ratnagiri',
                        'image' => 'regional-directors/sushil-petkar.jpg',
                        'order' => 3,
                    ],
                    [
                        'name' => 'Mr. Gajanan Chipkar',
                        'city' => 'Mumbai',
                        'region' => 'Mumbai+Thane+Raigad',
                        'image' => 'regional-directors/gajanan-chipkar.jpg',
                        'order' => 4,
                    ],
                    [
                        'name' => 'Mr. Gururaj Kulkarni',
                        'city' => 'Solapur',
                        'region' => 'Solapur',
                        'image' => 'regional-directors/gururaj-kulkarni.jpg',
                        'order' => 5,
                    ],
                    [
                        'name' => 'Mr. Swapnil Vernekar',
                        'city' => 'Solapur',
                        'region' => 'Solapur',
                        'image' => 'regional-directors/swapnil-vernekar.jpg',
                        'order' => 6,
                    ],
                    [
                        'name' => 'Mr. Shashikant Jamadar',
                        'city' => 'Solapur',
                        'region' => 'Solapur',
                        'image' => 'regional-directors/shashikant-jamadar.jpg',
                        'order' => 7,
                    ],
                    [
                        'name' => 'Mr. Sandip Desale',
                        'city' => 'Nashik',
                        'region' => 'Nashik',
                        'image' => 'regional-directors/sandip-desale.jpg',
                        'order' => 8,
                    ],
                    [
                        'name' => 'Mr. Gaurav Kapadnis',
                        'city' => 'Nashik',
                        'region' => 'Nashik',
                        'image' => 'regional-directors/gaurav-kapadnis.jpg',
                        'order' => 9,
                    ],
                    [
                        'name' => 'Mr. Rajendra Panchal',
                        'city' => 'Pune',
                        'region' => 'Pune',
                        'image' => 'regional-directors/rajendra-panchal.jpg',
                        'order' => 10,
                    ],
                    [
                        'name' => 'Mr. Ganesh Sutar',
                        'city' => 'Pune',
                        'region' => 'Pune',
                        'image' => 'regional-directors/ganesh-sutar.jpg',
                        'order' => 11,
                    ],
                    [
                        'name' => 'Mr. Dhanajirao Ekal',
                        'city' => 'Kolhapur',
                        'region' => 'Kolhapur+Sangli',
                        'image' => 'regional-directors/dhanajirao-ekal.jpg',
                        'order' => 12,
                    ],
                    [
                        'name' => 'Mr. Dhairyashil Jadhav',
                        'city' => 'Kolhapur',
                        'region' => 'Kolhapur+Sangli',
                        'image' => 'regional-directors/dhairyashil-jadhav.jpg',
                        'order' => 13,
                    ],
                ],
            ],
        ];

        foreach ($directors as $category) {
            foreach ($category['members'] as $member) {
                RegionalDirector::create([
                    'category_title' => $category['category_title'],
                    'category_icon' => $category['category_icon'],
                    'member_name' => $member['name'],
                    'member_city' => $member['city'],
                    'member_region' => $member['region'],
                    'member_image' => $member['image'],
                    'category_order' => $category['category_order'],
                    'member_order' => $member['order'],
                    'is_active' => true,
                ]);
            }
        }
    }
}