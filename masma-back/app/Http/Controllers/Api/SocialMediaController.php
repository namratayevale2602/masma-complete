<?php
// app/Http/Controllers/Api/SocialMediaController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialMediaController extends Controller
{
    /**
     * Get all active social media links
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $socialMedia = SocialMedia::getActiveSocialMedia();
        
        // Format the data to match what the React component expects
        $formattedData = $socialMedia->map(function ($social) {
            return [
                'id' => $social->id,
                'platform' => $social->platform,
                'icon' => $social->icon,
                'url' => $social->url,
                'color' => $social->color,
                'order' => $social->order,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedData,
        ]);
    }

    /**
     * Store a new social media link
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'url' => 'required|url|max:255',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        
        if (!isset($data['order'])) {
            $data['order'] = SocialMedia::max('order') + 1;
        }

        $social = SocialMedia::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Social media link created successfully',
            'data' => [
                'id' => $social->id,
                'platform' => $social->platform,
                'icon' => $social->icon,
                'url' => $social->url,
                'color' => $social->color,
                'order' => $social->order,
            ],
        ], 201);
    }

    /**
     * Display the specified social media link
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $social = SocialMedia::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $social->id,
                'platform' => $social->platform,
                'icon' => $social->icon,
                'url' => $social->url,
                'color' => $social->color,
                'order' => $social->order,
                'is_active' => $social->is_active,
            ],
        ]);
    }

    /**
     * Update the specified social media link
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $social = SocialMedia::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'platform' => 'sometimes|required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'url' => 'sometimes|required|url|max:255',
            'color' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $social->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Social media link updated successfully',
            'data' => [
                'id' => $social->id,
                'platform' => $social->platform,
                'icon' => $social->icon,
                'url' => $social->url,
                'color' => $social->color,
                'order' => $social->order,
                'is_active' => $social->is_active,
            ],
        ]);
    }

    /**
     * Remove the specified social media link
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $social = SocialMedia::findOrFail($id);
        $social->delete();

        return response()->json([
            'success' => true,
            'message' => 'Social media link deleted successfully',
        ]);
    }

    /**
     * Update the order of social media links
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:social_media,id',
            'items.*.order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->items as $item) {
            SocialMedia::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
    }

    /**
     * Toggle active status
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive($id)
    {
        $social = SocialMedia::findOrFail($id);
        $social->is_active = !$social->is_active;
        $social->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'is_active' => $social->is_active,
        ]);
    }
}