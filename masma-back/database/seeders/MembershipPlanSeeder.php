<?php
// database/seeders/MembershipPlanSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipPlan;
use App\Models\MembershipFeature;

class MembershipPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Create features first
        $features = [
            [
                'label' => 'Invitation for in Personal Meetings',
                'key' => 'invitationPersonalMeetings',
                'description' => 'Personal meeting invitations with industry leaders and members',
                'order' => 1,
            ],
            [
                'label' => 'Invitation for in Online Meetings',
                'key' => 'invitationOnlineMeetings',
                'description' => 'Access to online meetings and webinars',
                'order' => 2,
            ],
            [
                'label' => 'Access to Associate Members Group',
                'key' => 'accessAssociateGroup',
                'description' => 'Join associate members WhatsApp/Telegram group',
                'order' => 3,
            ],
            [
                'label' => 'Access to MASMA Main Group',
                'key' => 'accessMainGroup',
                'description' => 'Join MASMA main WhatsApp/Telegram group',
                'order' => 4,
            ],
            [
                'label' => 'Company Listing on MASMA Website',
                'key' => 'companyListing',
                'description' => 'Your company name and details listed on MASMA website',
                'order' => 5,
            ],
            [
                'label' => 'Advertisement on MASMA Official Groups',
                'key' => 'advertisementOfficialGroups',
                'description' => 'Post advertisements in official MASMA groups',
                'order' => 6,
            ],
            [
                'label' => 'Webinar Hosting',
                'key' => 'webinarHosting',
                'description' => 'Host webinars on MASMA platform',
                'order' => 7,
            ],
            [
                'label' => 'Advertisement on MASMA Website',
                'key' => 'advertisementWebsite',
                'description' => 'Display advertisements on MASMA website',
                'order' => 8,
            ],
            [
                'label' => 'Discounts on Event Sponsorship',
                'key' => 'discountEventSponsorship',
                'description' => 'Special discounts on event sponsorship opportunities',
                'order' => 9,
            ],
            [
                'label' => 'Discount on Exhibition Stalls',
                'key' => 'discountExhibitionStalls',
                'description' => 'Discounted rates for exhibition stalls at MASMA events',
                'order' => 10,
            ],
        ];

        foreach ($features as $feature) {
            MembershipFeature::updateOrCreate(
                ['key' => $feature['key']],
                $feature
            );
        }

        // Create membership plans
        $plans = [
            [
                'name' => 'Students',
                'type' => 'student',
                'membership_fee' => '₹1000',
                'registration_charges' => null,
                'duration' => 'Economic Year',
                'features' => [
                    'invitationPersonalMeetings' => 'Selective Events',
                    'invitationOnlineMeetings' => 'Yes',
                    'accessAssociateGroup' => 'Yes',
                    'accessMainGroup' => null,
                    'companyListing' => 'Yes',
                    'advertisementOfficialGroups' => null,
                    'webinarHosting' => null,
                    'advertisementWebsite' => null,
                    'discountEventSponsorship' => null,
                    'discountExhibitionStalls' => null,
                ],
                'order' => 1,
                'is_highlighted' => false,
            ],
            [
                'name' => 'EPC Classic',
                'type' => 'classic',
                'membership_fee' => '₹2500',
                'registration_charges' => null,
                'duration' => 'Economic Year',
                'features' => [
                    'invitationPersonalMeetings' => 'Yes',
                    'invitationOnlineMeetings' => 'Yes',
                    'accessAssociateGroup' => null,
                    'accessMainGroup' => 'Yes',
                    'companyListing' => 'Yes',
                    'advertisementOfficialGroups' => 'Add Charges',
                    'webinarHosting' => 'Add Charges',
                    'advertisementWebsite' => 'With Discounts',
                    'discountEventSponsorship' => null,
                    'discountExhibitionStalls' => null,
                ],
                'order' => 2,
                'is_highlighted' => false,
            ],
            [
                'name' => 'Dealer /Distributor',
                'type' => 'dealer',
                'membership_fee' => '₹5000',
                'registration_charges' => null,
                'duration' => 'Economic Year',
                'features' => [
                    'invitationPersonalMeetings' => 'Yes',
                    'invitationOnlineMeetings' => 'Yes',
                    'accessAssociateGroup' => null,
                    'accessMainGroup' => 'Yes',
                    'companyListing' => 'Yes',
                    'advertisementOfficialGroups' => '3/Month',
                    'webinarHosting' => '4/year',
                    'advertisementWebsite' => 'Yes',
                    'discountEventSponsorship' => 'Yes',
                    'discountExhibitionStalls' => 'Yes',
                ],
                'order' => 3,
                'is_highlighted' => false,
            ],
            [
                'name' => 'Corporate Silver',
                'type' => 'silver',
                'membership_fee' => '₹10000',
                'registration_charges' => null,
                'duration' => 'Economic Year',
                'features' => [
                    'invitationPersonalMeetings' => 'Yes',
                    'invitationOnlineMeetings' => 'Yes',
                    'accessAssociateGroup' => null,
                    'accessMainGroup' => 'Yes',
                    'companyListing' => 'Yes',
                    'advertisementOfficialGroups' => '4/Month',
                    'webinarHosting' => 'Yes',
                    'advertisementWebsite' => 'Yes',
                    'discountEventSponsorship' => 'Yes',
                    'discountExhibitionStalls' => 'Yes',
                ],
                'order' => 4,
                'is_highlighted' => false,
            ],
            [
                'name' => 'Corporate Gold',
                'type' => 'gold',
                'membership_fee' => '₹20000',
                'registration_charges' => null,
                'duration' => 'Economic Year',
                'features' => [
                    'invitationPersonalMeetings' => 'Yes',
                    'invitationOnlineMeetings' => 'Yes',
                    'accessAssociateGroup' => null,
                    'accessMainGroup' => 'Yes',
                    'companyListing' => 'Yes',
                    'advertisementOfficialGroups' => 'Yes',
                    'webinarHosting' => 'Yes',
                    'advertisementWebsite' => '3 Month Free',
                    'discountEventSponsorship' => 'Yes',
                    'discountExhibitionStalls' => 'Yes',
                ],
                'order' => 5,
                'is_highlighted' => true, // Mark as popular
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}