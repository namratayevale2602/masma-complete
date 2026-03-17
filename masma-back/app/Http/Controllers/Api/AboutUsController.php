<?php
// app/Http/Controllers/Api/AboutUsController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AboutUsController extends Controller
{
    /**
     * Get about us content
     */
    public function index()
    {
        $aboutUs = AboutUs::getActive();
        
        if (!$aboutUs) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'No about us content found'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $aboutUs->id,
                'title' => $aboutUs->title,
                'description' => $aboutUs->description,
                'image' => $aboutUs->image_url,
                'badge' => [
                    'number' => $aboutUs->badge_number,
                    'label' => $aboutUs->badge_label,
                    'subtext' => $aboutUs->badge_subtext,
                ],
                'button' => [
                    'text' => $aboutUs->button_text,
                    'link' => $aboutUs->button_link,
                ],
            ],
        ]);
    }

    /**
     * Store about us content
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'badge_number' => 'nullable|string|max:50',
            'badge_label' => 'nullable|string|max:100',
            'badge_subtext' => 'nullable|string|max:100',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['image']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_about_' . $file->getClientOriginalName();
            $path = $file->storeAs('about', $filename, 'uploads');
            $data['image'] = $path;
        }

        // If no active content exists, set as active
        if (!AboutUs::where('is_active', true)->exists()) {
            $data['is_active'] = true;
        }

        $aboutUs = AboutUs::create($data);

        return response()->json([
            'success' => true,
            'message' => 'About us content created successfully',
            'data' => $aboutUs,
        ], 201);
    }

    /**
     * Update about us content
     */
    public function update(Request $request, $id)
    {
        $aboutUs = AboutUs::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'badge_number' => 'nullable|string|max:50',
            'badge_label' => 'nullable|string|max:100',
            'badge_subtext' => 'nullable|string|max:100',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['image']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($aboutUs->image) {
                Storage::disk('uploads')->delete($aboutUs->image);
            }
            
            $file = $request->file('image');
            $filename = time() . '_about_' . $file->getClientOriginalName();
            $path = $file->storeAs('about', $filename, 'uploads');
            $data['image'] = $path;
        }

        $aboutUs->update($data);

        return response()->json([
            'success' => true,
            'message' => 'About us content updated successfully',
            'data' => $aboutUs,
        ]);
    }

    /**
     * Delete about us content
     */
    public function destroy($id)
    {
        $aboutUs = AboutUs::findOrFail($id);

        // Delete image
        if ($aboutUs->image) {
            Storage::disk('uploads')->delete($aboutUs->image);
        }

        $aboutUs->delete();

        return response()->json([
            'success' => true,
            'message' => 'About us content deleted successfully',
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        
        // If activating, deactivate all others
        if (!$aboutUs->is_active) {
            AboutUs::where('is_active', true)->update(['is_active' => false]);
        }
        
        $aboutUs->is_active = !$aboutUs->is_active;
        $aboutUs->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'is_active' => $aboutUs->is_active,
        ]);
    }
}