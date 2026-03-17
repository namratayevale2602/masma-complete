<?php
// database/seeders/ContactSettingsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactSetting;

class ContactSettingsSeeder extends Seeder
{
    public function run(): void
    {
        ContactSetting::create([
            'page_title' => 'Contact Us',
            'page_description' => 'Get in touch with MASMA for solar energy solutions, partnerships, and support. We\'re here to help you harness the power of the sun.',
            'office_address' => 'THE MAHARASHTRA SOLAR MANUFACTURES ASSOCIATION D-93, 4th Floor,Office No.93, G-Wing, S.No. 19A/3B,Pune - Satara Rd, KK Market, Ahilya devi chowk Dhankawadi, Pune, Maharashtra 411043',
            'phone' => '+91 93091 67947',
            'email' => 'info@masma.in',
            'working_hours_weekdays' => 'Monday - Friday: 9:00 AM - 6:00 PM',
            'working_hours_saturday' => 'Saturday: 9:00 AM - 2:00 PM',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15137.237191498001!2d73.860219!3d18.469644000000002!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2bf8ab1109a25%3A0xb010aff5c75f1c92!2sMaharashtra%20Solar%20Manufacturers%20Association!5e0!3m2!1sen!2sin!4v1763985561571!5m2!1sen!2sin',
            'form_title' => 'Send Us a Message',
            'form_description' => 'Fill out the form below and our team will get back to you within 24 hours.',
            'is_active' => true,
        ]);
    }
}