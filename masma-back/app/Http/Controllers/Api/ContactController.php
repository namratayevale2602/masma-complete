<?php
// app/Http/Controllers/Api/ContactController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Get contact page data from database
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get active contact settings from database
        $settings = ContactSetting::where('is_active', true)->first();
        
        // Get active social media links from database
        $socialMedia = SocialMedia::where('is_active', true)
            ->orderBy('order')
            ->get();

        // If no settings found in database, return empty data structure
        if (!$settings) {
            return response()->json([
                'success' => true,
                'data' => [
                    'page_title' => null,
                    'page_description' => null,
                    'contact_info' => [],
                    'form' => [
                        'title' => null,
                        'description' => null,
                    ],
                    'map_embed_url' => null,
                    'social_media' => $socialMedia->map(function ($social) {
                        return [
                            'id' => $social->id,
                            'platform' => $social->platform,
                            'icon' => $social->icon,
                            'url' => $social->url,
                            'color' => $social->color,
                        ];
                    }),
                ]
            ]);
        }

        // Build contact info from database fields
        $contactInfo = [];

        // Add office address if exists
        if ($settings->office_address) {
            $contactInfo[] = [
                'icon' => 'FaMapMarkerAlt',
                'title' => 'Our Office',
                'details' => [$settings->office_address]
            ];
        }

        // Add phone if exists
        if ($settings->phone) {
            $contactInfo[] = [
                'icon' => 'FaPhone',
                'title' => 'Phone Number',
                'details' => [$settings->phone]
            ];
        }

        // Add email if exists
        if ($settings->email) {
            $contactInfo[] = [
                'icon' => 'FaEnvelope',
                'title' => 'Email Address',
                'details' => [$settings->email]
            ];
        }

        // Add working hours if exists
        $workingHours = [];
        if ($settings->working_hours_weekdays) {
            $workingHours[] = $settings->working_hours_weekdays;
        }
        if ($settings->working_hours_saturday) {
            $workingHours[] = $settings->working_hours_saturday;
        }
        
        if (!empty($workingHours)) {
            $contactInfo[] = [
                'icon' => 'FaClock',
                'title' => 'Working Hours',
                'details' => $workingHours
            ];
        }

        // Return only data that exists in database
        return response()->json([
            'success' => true,
            'data' => [
                'page_title' => $settings->page_title,
                'page_description' => $settings->page_description,
                'contact_info' => $contactInfo,
                'form' => [
                    'title' => $settings->form_title,
                    'description' => $settings->form_description,
                ],
                'map_embed_url' => $settings->map_embed_url,
                'social_media' => $socialMedia->map(function ($social) {
                    return [
                        'id' => $social->id,
                        'platform' => $social->platform,
                        'icon' => $social->icon,
                        'url' => $social->url,
                        'color' => $social->color,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Test mail endpoint
     */
    public function testMail()
    {
        return response()->json([
            'success' => true,
            'message' => 'Test mail endpoint working'
        ]);
    }

    /**
     * Submit contact form
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'consent' => 'required|boolean'
        ]);

        // Here you can save to database if you have a ContactMessage model
        // ContactMessage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your message! We\'ll get back to you soon.',
            'data' => $validated
        ], 201);
    }
}