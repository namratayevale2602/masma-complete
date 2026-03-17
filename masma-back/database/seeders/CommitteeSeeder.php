<?php
// database/seeders/CommitteeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Committee;
use Illuminate\Support\Facades\Storage;

class CommitteeSeeder extends Seeder
{
    public function run(): void
    {
        $committees = [
            // Public Relations Committee
            [
                'category_title' => 'Public Relations Committee',
                'category_icon' => 'FaUserTie',
                'category_order' => 1,
                'members' => [
                    [
                        'name' => 'Mr. Amit Kulkarni',
                        'city' => 'Nashik',
                        'position' => 'President',
                        'image' => 'committees/amit-kulkarni.jpg',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Mr. Pradip Khade',
                        'city' => 'Kolhapur',
                        'position' => 'Voice President',
                        'image' => 'committees/pradip-khade.jpg',
                        'order' => 2,
                    ],
                    [
                        'name' => 'Mr. Sahaj Mutha',
                        'city' => 'Pune',
                        'position' => 'Secretary',
                        'image' => 'committees/sahaj-mutha.jpg',
                        'order' => 3,
                    ],
                    [
                        'name' => 'Mr. Chinmay Kulkarni',
                        'city' => 'Pune',
                        'position' => 'Treasurer',
                        'image' => 'committees/chinmay-kulkarni.jpg',
                        'order' => 4,
                    ],
                    [
                        'name' => 'Mr. Shashikant Wakade',
                        'city' => 'Pune',
                        'position' => 'Imm. Past President',
                        'image' => 'committees/shashikant-wakade.jpg',
                        'order' => 5,
                    ],
                    [
                        'name' => 'Mr. Manisha Barbind',
                        'city' => 'Ch.SambhajiNagar',
                        'position' => 'Director',
                        'image' => 'committees/manisha-barbind.jpg',
                        'order' => 6,
                    ],
                    [
                        'name' => 'Mr. Bhartesh Dhooli',
                        'city' => 'Pune',
                        'position' => 'Director',
                        'image' => 'committees/bhartesh-dhooli.jpg',
                        'order' => 7,
                    ],
                ],
            ],
            
            // Women Entrepreneur's Committee
            [
                'category_title' => "Women Entrepreneur's Committee",
                'category_icon' => 'FaUserShield',
                'category_order' => 2,
                'members' => [
                    [
                        'name' => 'Riya Mahajani',
                        'city' => 'Pune',
                        'position' => 'Committee head',
                        'image' => 'committees/riya-mahajani.jpg',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Vaibhavi Kop',
                        'city' => 'Kolhapur',
                        'position' => 'Committee Member',
                        'image' => 'committees/vaibhavi-kop.jpg',
                        'order' => 2,
                    ],
                ],
            ],
            
            // Legal Committee
            [
                'category_title' => 'Legal Committee',
                'category_icon' => 'FaCrown',
                'category_order' => 3,
                'members' => [
                    [
                        'name' => 'Mr. Narendra Pawar',
                        'city' => 'pune',
                        'position' => 'Committee Head',
                        'image' => 'committees/narendra-pawar.jpg',
                        'order' => 1,
                    ],
                ],
            ],
            
            // Membership Committee
            [
                'category_title' => 'Membership Committee',
                'category_icon' => 'FaUsers',
                'category_order' => 4,
                'members' => [
                    [
                        'name' => 'Mr. Atul Honole',
                        'city' => 'Kolhapur',
                        'position' => 'Committee Head',
                        'image' => 'committees/atul-honole.jpg',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Mr. Dnyanesh Deshpande',
                        'city' => 'Nashik',
                        'position' => 'Committee Member',
                        'image' => 'committees/dnyanesh-deshpande.jpg',
                        'order' => 2,
                    ],
                ],
            ],
            
            // Young Entrepreneur's Committee
            [
                'category_title' => "Young Entrepreneur's Committee",
                'category_icon' => 'FaUserGraduate',
                'category_order' => 5,
                'members' => [
                    [
                        'name' => 'Mr. Shrinidhi N Kulkarni',
                        'city' => 'Kolhapur',
                        'position' => 'Committee Head',
                        'image' => 'committees/shrinidhi-kulkarni.jpg',
                        'order' => 1,
                    ],
                    [
                        'name' => 'Mr. Akshay Wakade',
                        'city' => 'Pune',
                        'position' => 'Committee Member',
                        'image' => 'committees/akshay-wakade.jpg',
                        'order' => 2,
                    ],
                    [
                        'name' => 'Mr. Suraj Doke',
                        'city' => 'Solapur',
                        'position' => 'Committee Member',
                        'image' => 'committees/suraj-doke.jpg',
                        'order' => 3,
                    ],
                ],
            ],
        ];

        foreach ($committees as $category) {
            foreach ($category['members'] as $index => $member) {
                Committee::create([
                    'category_title' => $category['category_title'],
                    'category_icon' => $category['category_icon'],
                    'member_name' => $member['name'],
                    'member_city' => $member['city'],
                    'member_position' => $member['position'],
                    'member_image' => $member['image'],
                    'category_order' => $category['category_order'],
                    'member_order' => $member['order'],
                    'is_active' => true,
                ]);
            }
        }
    }
}