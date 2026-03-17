<?php
// database/seeders/FaqSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'WHAT IS A SOLAR ROOFTOP SYSTEM?',
                'answer' => 'A solar rooftop system is a photovoltaic system that converts sunlight directly into electricity using solar panels installed on the rooftop of residential, commercial, or industrial buildings. These systems help reduce electricity bills and carbon footprint while providing clean, renewable energy.',
                'category' => 'Basics',
                'order' => 1,
            ],
            [
                'question' => 'WHAT IS A GRID CONNECTED SOLAR ROOFTOP SYSTEM?',
                'answer' => 'In grid connected rooftop or small SPV system, the DC power generated from SPV panel is converted to AC power using power conditioning unit/Inverter and is fed to the grid either of 440/220 Volt three/single phase line or of 33 kV/11 kV three phase lines depending on the capacity of the system installed at residential, institution/commercial establishment and the regulatory framework specified for respective States.',
                'category' => 'Technical',
                'order' => 2,
            ],
            [
                'question' => 'WHAT ARE THE BENEFITS OF SOLAR ROOFTOP SYSTEM?',
                'answer' => "1. Reduces electricity bills\n2. Low maintenance costs\n3. Environmentally friendly\n4. Energy independence\n5. Tax benefits and subsidies\n6. Increases property value\n7. Long lifespan (25+ years)\n8. Silent operation",
                'category' => 'Basics',
                'order' => 3,
            ],
            [
                'question' => 'HOW MUCH ROOF AREA IS REQUIRED FOR SOLAR PANELS?',
                'answer' => 'Typically, 1 kW of solar panels requires approximately 100-120 square feet of shadow-free roof area. For a 5 kW system, you would need about 500-600 square feet of roof space.',
                'category' => 'Technical',
                'order' => 4,
            ],
            [
                'question' => 'WHAT IS NET METERING?',
                'answer' => 'Net metering is a billing mechanism that credits solar energy system owners for the electricity they add to the grid. For example, if a residential customer has a PV system on the roof, it may generate more electricity than the home uses during daylight hours. With net metering, the customer is credited for that excess electricity, usually at the retail rate.',
                'category' => 'Policy',
                'order' => 5,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}