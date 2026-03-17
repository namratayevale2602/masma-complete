<?php
// app/Http/Controllers/Api/HeroImageController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroImageController extends Controller
{
    /**
     * Get all active hero images
     */
    public function index()
    {
        $heroImages = HeroImage::active()->get();
        
        return response()->json([
            'success' => true,
            'data' => $heroImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'desktop' => $image->desktop_image_url,
                    'mobile' => $image->mobile_image_url,
                    'alt_text' => $image->alt_text,
                    'order' => $image->order,
                ];
            }),
        ]);
    }

    /**
     * Store a newly created hero image
     */
    public function store(Request $request)
    {
        $request->validate([
            'desktop_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $data = $request->except(['desktop_image', 'mobile_image']);

        // Handle desktop image upload using uploads disk
        if ($request->hasFile('desktop_image')) {
            $path = $request->file('desktop_image')->store('hero/desktop', 'uploads');
            $data['desktop_image'] = $path; // This will store: hero/desktop/filename.jpg
        }

        // Handle mobile image upload using uploads disk
        if ($request->hasFile('mobile_image')) {
            $path = $request->file('mobile_image')->store('hero/mobile', 'uploads');
            $data['mobile_image'] = $path; // This will store: hero/mobile/filename.jpg
        }

        $heroImage = HeroImage::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Hero image created successfully',
            'data' => $heroImage,
        ], 201);
    }

    /**
     * Display the specified hero image
     */
    public function show($id)
    {
        $heroImage = HeroImage::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $heroImage->id,
                'desktop' => $heroImage->desktop_image_url,
                'mobile' => $heroImage->mobile_image_url,
                'alt_text' => $heroImage->alt_text,
                'order' => $heroImage->order,
                'is_active' => $heroImage->is_active,
            ],
        ]);
    }

    /**
     * Update the specified hero image
     */
    public function update(Request $request, $id)
    {
        $heroImage = HeroImage::findOrFail($id);

        $request->validate([
            'desktop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->except(['desktop_image', 'mobile_image']);

        // Handle desktop image upload
        if ($request->hasFile('desktop_image')) {
            // Delete old image
            if ($heroImage->desktop_image) {
                Storage::disk('uploads')->delete($heroImage->desktop_image);
            }
            
            $path = $request->file('desktop_image')->store('hero/desktop', 'uploads');
            $data['desktop_image'] = $path;
        }

        // Handle mobile image upload
        if ($request->hasFile('mobile_image')) {
            // Delete old image
            if ($heroImage->mobile_image) {
                Storage::disk('uploads')->delete($heroImage->mobile_image);
            }
            
            $path = $request->file('mobile_image')->store('hero/mobile', 'uploads');
            $data['mobile_image'] = $path;
        }

        $heroImage->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Hero image updated successfully',
            'data' => $heroImage,
        ]);
    }

    /**
     * Remove the specified hero image
     */
    public function destroy($id)
    {
        $heroImage = HeroImage::findOrFail($id);

        // Delete images from uploads disk
        if ($heroImage->desktop_image) {
            Storage::disk('uploads')->delete($heroImage->desktop_image);
        }
        
        if ($heroImage->mobile_image) {
            Storage::disk('uploads')->delete($heroImage->mobile_image);
        }

        $heroImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hero image deleted successfully',
        ]);
    }
}