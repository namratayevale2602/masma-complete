<?php
// database/seeders/CircularSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Circular;

class CircularSeeder extends Seeder
{
    public function run(): void
    {
        $circulars = [
            // Important Circular section
            [
                'title' => 'Empanellment Procedure',
                'description' => 'Detailed procedure for empanellment of solar installers and vendors',
                'file_path' => 'circulars/empanellment-procedure.pdf',
                'file_type' => 'pdf',
                'category' => 'Important Circular',
                'subcategory' => 'Circulars',
                'order' => 1,
            ],
            [
                'title' => 'MNRE 16.03.2023_PM Surya Ghar Muft Bijli Yojana',
                'description' => 'PM Surya Ghar Muft Bijli Yojana scheme details and guidelines',
                'file_path' => 'circulars/pm-surya-ghar-yojana.pdf',
                'file_type' => 'pdf',
                'category' => 'Important Circular',
                'subcategory' => 'Circulars',
                'order' => 2,
            ],
            
            // Important Documents section
            [
                'title' => 'Timely processing of Rooftop RE application',
                'description' => 'Guidelines for timely processing of rooftop renewable energy applications',
                'file_path' => 'circulars/timely-processing-rooftop-application.pdf',
                'file_type' => 'pdf',
                'category' => 'Important Documents',
                'subcategory' => 'Procedures & Forms',
                'order' => 1,
            ],
            [
                'title' => 'Procedure for Application for connectivity of Renewable Energy Generating System with MSEDCL\'s Network',
                'description' => 'Step-by-step procedure for connecting renewable energy systems to MSEDCL network',
                'file_path' => 'circulars/connectivity-procedure.pdf',
                'file_type' => 'pdf',
                'category' => 'Important Documents',
                'subcategory' => 'Procedures & Forms',
                'order' => 2,
            ],
            [
                'title' => 'Application Form for installation of Renewable Energy Generating System under Net Metering Arrangement or Net Billing Arrangement',
                'description' => 'Official application form for net metering and net billing arrangements',
                'file_path' => 'circulars/application-form-net-metering.pdf',
                'file_type' => 'pdf',
                'category' => 'Important Documents',
                'subcategory' => 'Procedures & Forms',
                'order' => 3,
            ],
            [
                'title' => 'Net Metering Connection Agreement',
                'description' => 'Standard agreement for net metering connections with distribution companies',
                'file_path' => 'circulars/net-metering-agreement.pdf',
                'file_type' => 'pdf',
                'category' => 'Important Documents',
                'subcategory' => 'Procedures & Forms',
                'order' => 4,
            ],
            [
                'title' => 'Net Billing Connection Agreement',
                'description' => 'Standard agreement for net billing connections with distribution companies',
                'file_path' => 'circulars/net-billing-agreement.pdf',
                'file_type' => 'pdf',
                'category' => 'Important Documents',
                'subcategory' => 'Procedures & Forms',
                'order' => 5,
            ],
            [
                'title' => 'Prior Intimation for Installation of Renewable Energy Generator behind the Consumer\'s Meter',
                'description' => 'Form for prior intimation of renewable energy generator installation',
                'file_path' => 'circulars/prior-intimation-form.pdf',
                'file_type' => 'pdf',
                'category' => 'Important Documents',
                'subcategory' => 'Procedures & Forms',
                'order' => 6,
            ],
            [
                'title' => 'Commercial Circular-No.322',
                'description' => 'Commercial circular regarding renewable energy policies and regulations',
                'file_path' => 'circulars/commercial-circular-322.pdf',
                'file_type' => 'pdf',
                'category' => 'Important Documents',
                'subcategory' => 'Procedures & Forms',
                'order' => 7,
            ],
        ];

        foreach ($circulars as $circular) {
            Circular::create($circular);
        }
    }
}