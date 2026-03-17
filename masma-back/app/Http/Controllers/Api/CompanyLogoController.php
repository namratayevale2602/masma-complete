<?php
// app/Http/Controllers/Api/CompanyLogoController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CompanyLogoController extends Controller
{
    /**
     * Get all active company logos
     */
    public function index()
    {
        $logos = CompanyLogo::getActiveLogos();
        
        return response()->json([
            'success' => true,
            'data' => $logos->map(function ($logo) {
                return [
                    'id' => $logo->id,
                    'image' => $logo->image_url,
                    'order' => $logo->order,
                ];
            }),
        ]);
    }

    /**
     * Get a single company logo
     */
    public function show($id)
    {
        $logo = CompanyLogo::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $logo->id,
                'image' => $logo->image_url,
                'order' => $logo->order,
                'is_active' => $logo->is_active,
            ],
        ]);
    }

    /**
     * Store a new company logo
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'order' => 'nullable|integer',
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
            // Preserve original filename with timestamp
            $filename = time() . '_logo_' . $file->getClientOriginalName();
            $path = $file->storeAs('company-logos', $filename, 'uploads');
            $data['image'] = $path;
        }

        // Set order if not provided
        if (!isset($data['order'])) {
            $maxOrder = CompanyLogo::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $logo = CompanyLogo::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Company logo created successfully',
            'data' => $logo,
        ], 201);
    }

    /**
     * Update a company logo
     */
    public function update(Request $request, $id)
    {
        $logo = CompanyLogo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'order' => 'nullable|integer',
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
            if ($logo->image) {
                Storage::disk('uploads')->delete($logo->image);
            }
            
            $file = $request->file('image');
            $filename = time() . '_logo_' . $file->getClientOriginalName();
            $path = $file->storeAs('company-logos', $filename, 'uploads');
            $data['image'] = $path;
        }

        $logo->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Company logo updated successfully',
            'data' => $logo,
        ]);
    }

    /**
     * Delete a company logo
     */
    public function destroy($id)
    {
        $logo = CompanyLogo::findOrFail($id);

        // Delete image
        if ($logo->image) {
            Storage::disk('uploads')->delete($logo->image);
        }

        $logo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company logo deleted successfully',
        ]);
    }

    /**
     * Update order of logos
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logos' => 'required|array',
            'logos.*.id' => 'required|exists:company_logos,id',
            'logos.*.order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->logos as $item) {
            CompanyLogo::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
    }
}