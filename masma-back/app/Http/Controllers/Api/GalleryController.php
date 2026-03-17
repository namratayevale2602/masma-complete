<?php
// app/Http/Controllers/Api/GalleryController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::getActiveGalleries();
        
        return response()->json([
            'success' => true,
            'data' => $galleries->map(function ($gallery) {
                return [
                    'id' => $gallery->id,
                    'title' => $gallery->title,
                    'image' => $gallery->featured_image_url,
                    'images' => $gallery->image_urls,
                    'order' => $gallery->order,
                ];
            }),
        ]);
    }

    public function show($id)
    {
        $gallery = Gallery::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $gallery->id,
                'title' => $gallery->title,
                'featured_image' => $gallery->featured_image_url,
                'images' => $gallery->image_urls,
                'order' => $gallery->order,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = [
            'title' => $request->title,
            'order' => $request->order ?? Gallery::max('order') + 1,
        ];

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = time() . '_featured_' . $file->getClientOriginalName();
            $path = $file->storeAs('gallery', $filename, 'uploads');
            $data['featured_image'] = $path;
        }

        // Handle multiple images
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('gallery', $filename, 'uploads');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $gallery = Gallery::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Gallery created successfully',
            'data' => $gallery,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['featured_image', 'images']);

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            if ($gallery->featured_image) {
                Storage::disk('uploads')->delete($gallery->featured_image);
            }
            $file = $request->file('featured_image');
            $filename = time() . '_featured_' . $file->getClientOriginalName();
            $path = $file->storeAs('gallery', $filename, 'uploads');
            $data['featured_image'] = $path;
        }

        // Handle multiple images
        if ($request->hasFile('images')) {
            // Delete old images
            if ($gallery->images) {
                foreach ($gallery->images as $oldImage) {
                    Storage::disk('uploads')->delete($oldImage);
                }
            }
            
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('gallery', $filename, 'uploads');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $gallery->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Gallery updated successfully',
            'data' => $gallery,
        ]);
    }

    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        
        // Delete featured image
        if ($gallery->featured_image) {
            Storage::disk('uploads')->delete($gallery->featured_image);
        }
        
        // Delete all images
        if ($gallery->images) {
            foreach ($gallery->images as $image) {
                Storage::disk('uploads')->delete($image);
            }
        }
        
        $gallery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gallery deleted successfully',
        ]);
    }
}