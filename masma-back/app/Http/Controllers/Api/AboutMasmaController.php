<?php
// app/Http/Controllers/Api/AboutMasmaController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutMasma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AboutMasmaController extends Controller
{
    /**
     * Get active about masma content
     */
    public function index()
    {
        $aboutMasma = AboutMasma::getActive();
        
        if (!$aboutMasma) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'No about masma content found'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $aboutMasma->id,
                'title' => $aboutMasma->title,
                'president' => [
                    'name' => $aboutMasma->president_name,
                    'title' => $aboutMasma->president_title,
                    'image' => $aboutMasma->president_image_url,
                    'message' => $aboutMasma->president_message,
                    'message_2' => $aboutMasma->president_message_2,
                    'message_3' => $aboutMasma->president_message_3,
                ],
                'stats' => $aboutMasma->stats,
                'order' => $aboutMasma->order,
            ],
        ]);
    }

    /**
     * Get a specific about masma content
     */
    public function show($id)
    {
        $aboutMasma = AboutMasma::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $aboutMasma->id,
                'title' => $aboutMasma->title,
                'president_name' => $aboutMasma->president_name,
                'president_title' => $aboutMasma->president_title,
                'president_image' => $aboutMasma->president_image_url,
                'president_message' => $aboutMasma->president_message,
                'president_message_2' => $aboutMasma->president_message_2,
                'president_message_3' => $aboutMasma->president_message_3,
                'stats' => $aboutMasma->stats,
                'order' => $aboutMasma->order,
                'is_active' => $aboutMasma->is_active,
            ],
        ]);
    }

    /**
     * Store about masma content
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'president_name' => 'nullable|string|max:255',
            'president_title' => 'nullable|string|max:255',
            'president_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'president_message' => 'nullable|string',
            'president_message_2' => 'nullable|string',
            'president_message_3' => 'nullable|string',
            'stats_1_label' => 'nullable|string|max:100',
            'stats_1_value' => 'nullable|string|max:50',
            'stats_2_label' => 'nullable|string|max:100',
            'stats_2_value' => 'nullable|string|max:50',
            'stats_3_label' => 'nullable|string|max:100',
            'stats_3_value' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['president_image']);

        // Handle president image upload
        if ($request->hasFile('president_image')) {
            $file = $request->file('president_image');
            $filename = time() . '_president_' . $file->getClientOriginalName();
            $path = $file->storeAs('about-masma', $filename, 'uploads');
            $data['president_image'] = $path;
        }

        // If no active content exists, set as active
        if (!AboutMasma::where('is_active', true)->exists()) {
            $data['is_active'] = true;
        }

        $aboutMasma = AboutMasma::create($data);

        return response()->json([
            'success' => true,
            'message' => 'About Masma content created successfully',
            'data' => $aboutMasma,
        ], 201);
    }

    /**
     * Update about masma content
     */
    public function update(Request $request, $id)
    {
        $aboutMasma = AboutMasma::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'president_name' => 'nullable|string|max:255',
            'president_title' => 'nullable|string|max:255',
            'president_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'president_message' => 'nullable|string',
            'president_message_2' => 'nullable|string',
            'president_message_3' => 'nullable|string',
            'stats_1_label' => 'nullable|string|max:100',
            'stats_1_value' => 'nullable|string|max:50',
            'stats_2_label' => 'nullable|string|max:100',
            'stats_2_value' => 'nullable|string|max:50',
            'stats_3_label' => 'nullable|string|max:100',
            'stats_3_value' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['president_image']);

        // Handle president image upload
        if ($request->hasFile('president_image')) {
            // Delete old image
            if ($aboutMasma->president_image) {
                Storage::disk('uploads')->delete($aboutMasma->president_image);
            }
            
            $file = $request->file('president_image');
            $filename = time() . '_president_' . $file->getClientOriginalName();
            $path = $file->storeAs('about-masma', $filename, 'uploads');
            $data['president_image'] = $path;
        }

        $aboutMasma->update($data);

        return response()->json([
            'success' => true,
            'message' => 'About Masma content updated successfully',
            'data' => $aboutMasma,
        ]);
    }

    /**
     * Delete about masma content
     */
    public function destroy($id)
    {
        $aboutMasma = AboutMasma::findOrFail($id);

        // Delete image
        if ($aboutMasma->president_image) {
            Storage::disk('uploads')->delete($aboutMasma->president_image);
        }

        $aboutMasma->delete();

        return response()->json([
            'success' => true,
            'message' => 'About Masma content deleted successfully',
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $aboutMasma = AboutMasma::findOrFail($id);
        
        // If activating, deactivate all others
        if (!$aboutMasma->is_active) {
            AboutMasma::where('is_active', true)->update(['is_active' => false]);
        }
        
        $aboutMasma->is_active = !$aboutMasma->is_active;
        $aboutMasma->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'is_active' => $aboutMasma->is_active,
        ]);
    }
}