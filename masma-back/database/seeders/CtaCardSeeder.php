<?php
// database/seeders/CtaCardSeeder.php

namespace Database\Seeders;

use App\Models\CtaCard;
use Illuminate\Database\Seeder;

class CtaCardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            [
                'title' => 'Exhibitor',
                'description' => 'Book your booth space and showcase your products to 15,000+ professionals',
                'icon' => 'FaIndustry',
                'color' => '#005aa8',
                'stats' => '500+ Booths',
                'link' => 'https://masmaexpo.in/exhibitor',
                'button_text' => 'Register',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Visitor',
                'description' => 'Register for free entry and access to latest renewable energy technologies',
                'icon' => 'FaUsers',
                'color' => '#ed6605',
                'stats' => '15,000+ Visitors',
                'link' => 'https://vms.ruha.co.in/registration/masma-visitor',
                'button_text' => 'Register',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Member',
                'description' => 'Join as member for exclusive access to conferences and roundtables',
                'icon' => 'FaUserTie',
                'color' => '#005aa8',
                'stats' => '200+ Speakers',
                'link' => '/bemember',
                'button_text' => 'Register',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($cards as $card) {
            CtaCard::create($card);
        }
    }
}