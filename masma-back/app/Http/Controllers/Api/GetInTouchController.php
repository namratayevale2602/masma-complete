<?php
// app/Http/Controllers/Api/GetInTouchController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GetInTouch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GetInTouchController extends Controller
{
    /**
     * Get active get in touch content
     */
    public function index()
    {
        $getInTouch = GetInTouch::getActive();
        
        if (!$getInTouch) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'No get in touch content found'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $getInTouch->id,
                'title' => $getInTouch->title,
                'main_title' => $getInTouch->main_title,
                'description' => $getInTouch->description,
                'background_image' => $getInTouch->background_image_url,
                'button' => [
                    'text' => $getInTouch->button_text,
                    'link' => $getInTouch->button_link,
                    'icon' => $getInTouch->button_icon,
                ],
            ],
        ]);
    }

    /**
     * Store get in touch content
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'main_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'button_icon' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['background_image']);

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $filename = time() . '_getintouch_' . $file->getClientOriginalName();
            $path = $file->storeAs('get-in-touch', $filename, 'uploads');
            $data['background_image'] = $path;
        }

        // If no active content exists, set as active
        if (!GetInTouch::where('is_active', true)->exists()) {
            $data['is_active'] = true;
        }

        $getInTouch = GetInTouch::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Get in touch content created successfully',
            'data' => $getInTouch,
        ], 201);
    }

    /**
     * Show specific get in touch content
     */
    public function show($id)
    {
        $getInTouch = GetInTouch::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $getInTouch->id,
                'title' => $getInTouch->title,
                'main_title' => $getInTouch->main_title,
                'description' => $getInTouch->description,
                'background_image' => $getInTouch->background_image_url,
                'button_text' => $getInTouch->button_text,
                'button_link' => $getInTouch->button_link,
                'button_icon' => $getInTouch->button_icon,
                'is_active' => $getInTouch->is_active,
            ],
        ]);
    }

    /**
     * Update get in touch content
     */
    public function update(Request $request, $id)
    {
        $getInTouch = GetInTouch::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'main_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'button_icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['background_image']);

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            // Delete old image
            if ($getInTouch->background_image) {
                Storage::disk('uploads')->delete($getInTouch->background_image);
            }
            
            $file = $request->file('background_image');
            $filename = time() . '_getintouch_' . $file->getClientOriginalName();
            $path = $file->storeAs('get-in-touch', $filename, 'uploads');
            $data['background_image'] = $path;
        }

        $getInTouch->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Get in touch content updated successfully',
            'data' => $getInTouch,
        ]);
    }

    /**
     * Delete get in touch content
     */
    public function destroy($id)
    {
        $getInTouch = GetInTouch::findOrFail($id);

        // Delete background image
        if ($getInTouch->background_image) {
            Storage::disk('uploads')->delete($getInTouch->background_image);
        }

        $getInTouch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Get in touch content deleted successfully',
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $getInTouch = GetInTouch::findOrFail($id);
        
        // If activating, deactivate all others
        if (!$getInTouch->is_active) {
            GetInTouch::where('is_active', true)->update(['is_active' => false]);
        }
        
        $getInTouch->is_active = !$getInTouch->is_active;
        $getInTouch->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'is_active' => $getInTouch->is_active,
        ]);
    }
}