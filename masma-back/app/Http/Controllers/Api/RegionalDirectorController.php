<?php
// app/Http/Controllers/Api/RegionalDirectorController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegionalDirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RegionalDirectorController extends Controller
{
    public function index()
    {
        $groupedDirectors = RegionalDirector::getGroupedByCategory();
        
        $formattedData = [];
        foreach ($groupedDirectors as $category => $members) {
            $formattedData[] = [
                'id' => $members->first()->id,
                'title' => $category,
                'icon' => $members->first()->category_icon,
                'members' => $members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->member_name,
                        'city' => $member->member_city,
                        'position' => $member->member_region,
                        'image' => $member->member_image_url,
                    ];
                }),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $formattedData,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_title' => 'required|string|max:255',
            'category_icon' => 'nullable|string|max:50',
            'member_name' => 'required|string|max:255',
            'member_city' => 'required|string|max:255',
            'member_region' => 'required|string|max:255',
            'member_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category_order' => 'nullable|integer',
            'member_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['member_image']);

        if ($request->hasFile('member_image')) {
            $file = $request->file('member_image');
            $filename = time() . '_director_' . $file->getClientOriginalName();
            $path = $file->storeAs('regional-directors', $filename, 'uploads');
            $data['member_image'] = $path;
        }

        $director = RegionalDirector::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Regional director created successfully',
            'data' => $director,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $director = RegionalDirector::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_title' => 'sometimes|required|string|max:255',
            'category_icon' => 'nullable|string|max:50',
            'member_name' => 'sometimes|required|string|max:255',
            'member_city' => 'sometimes|required|string|max:255',
            'member_region' => 'sometimes|required|string|max:255',
            'member_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category_order' => 'nullable|integer',
            'member_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['member_image']);

        if ($request->hasFile('member_image')) {
            if ($director->member_image) {
                Storage::disk('uploads')->delete($director->member_image);
            }
            $file = $request->file('member_image');
            $filename = time() . '_director_' . $file->getClientOriginalName();
            $path = $file->storeAs('regional-directors', $filename, 'uploads');
            $data['member_image'] = $path;
        }

        $director->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Regional director updated successfully',
            'data' => $director,
        ]);
    }

    public function destroy($id)
    {
        $director = RegionalDirector::findOrFail($id);
        
        if ($director->member_image) {
            Storage::disk('uploads')->delete($director->member_image);
        }
        
        $director->delete();

        return response()->json([
            'success' => true,
            'message' => 'Regional director deleted successfully',
        ]);
    }
}