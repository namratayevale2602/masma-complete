<?php
// database/seeders/AssociateSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Associate;

class AssociateSeeder extends Seeder
{
    public function run(): void
    {
        $associates = [
            [
                'company_name' => 'Dnyanada Institute of Flow Piping Technology',
                'industry' => 'Solar Panel Manufacturing',
                'description' => 'DIFPT was established in 2010 to provide Plumbing Technology Skill training course of short duration of only 60 days to school & college youths from rural area.',
                'logo' => 'associates/dnyanada.jpg',
                'order' => 1,
            ],
            [
                'company_name' => 'MIT',
                'industry' => 'Solar Installation & Maintenance',
                'description' => 'MIT World Peace University (MITWPU), Pune is one of the leading private institutions in India with a firm belief that the "Union of Science and Spirituality alone will bring peace to mankind".',
                'logo' => 'associates/mit.jpg',
                'order' => 2,
            ],
            [
                'company_name' => 'Global India Business Forum',
                'industry' => 'Solar Technology R&D',
                'description' => 'Pioneering research in advanced solar technologies',
                'logo' => 'associates/global-india.jpg',
                'order' => 3,
            ],
            [
                'company_name' => 'Pune Construction Engineering Research Foundation',
                'industry' => 'Residential Solar Solutions',
                'description' => 'Affordable solar solutions for homes and communities',
                'logo' => 'associates/pcerf.jpg',
                'order' => 4,
            ],
            [
                'company_name' => 'Gesellschaft Fur Internationale Zusammenarbeit (GIZ)',
                'industry' => 'Commercial Solar Projects',
                'description' => 'Large-scale solar projects for businesses and industries',
                'logo' => 'associates/giz.jpg',
                'order' => 5,
            ],
            [
                'company_name' => 'UzEnergyExpo',
                'industry' => 'Solar Component Suppliers',
                'description' => 'Quality components for solar system installations',
                'logo' => 'associates/uz-energy-expo.jpg',
                'order' => 6,
            ],
            [
                'company_name' => 'YES Bank',
                'industry' => 'Solar Water Heating',
                'description' => 'Specialized in solar water heating solutions',
                'logo' => 'associates/yes-bank.jpg',
                'order' => 7,
            ],
            [
                'company_name' => 'Pune peoples co operative bank',
                'industry' => 'Solar Consulting & Advisory',
                'description' => 'Expert consulting services for solar projects',
                'logo' => 'associates/ppcob.jpg',
                'order' => 8,
            ],
        ];

        foreach ($associates as $associate) {
            Associate::create($associate);
        }
    }
}