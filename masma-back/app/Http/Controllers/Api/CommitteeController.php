<?php
// app/Http/Controllers/Api/CommitteeController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CommitteeController extends Controller
{
    public function index()
    {
        $groupedCommittees = Committee::getGroupedByCategory();
        
        $formattedData = [];
        foreach ($groupedCommittees as $category => $members) {
            $formattedData[] = [
                'id' => $members->first()->id,
                'title' => $category,
                'icon' => $members->first()->category_icon,
                'members' => $members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->member_name,
                        'city' => $member->member_city,
                        'position' => $member->member_position,
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
            'member_position' => 'required|string|max:255',
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
            $filename = time() . '_committee_' . $file->getClientOriginalName();
            $path = $file->storeAs('committees', $filename, 'uploads');
            $data['member_image'] = $path;
        }

        $committee = Committee::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Committee member created successfully',
            'data' => $committee,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $committee = Committee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_title' => 'sometimes|required|string|max:255',
            'category_icon' => 'nullable|string|max:50',
            'member_name' => 'sometimes|required|string|max:255',
            'member_city' => 'sometimes|required|string|max:255',
            'member_position' => 'sometimes|required|string|max:255',
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
            if ($committee->member_image) {
                Storage::disk('uploads')->delete($committee->member_image);
            }
            $file = $request->file('member_image');
            $filename = time() . '_committee_' . $file->getClientOriginalName();
            $path = $file->storeAs('committees', $filename, 'uploads');
            $data['member_image'] = $path;
        }

        $committee->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Committee member updated successfully',
            'data' => $committee,
        ]);
    }

    public function destroy($id)
    {
        $committee = Committee::findOrFail($id);
        
        if ($committee->member_image) {
            Storage::disk('uploads')->delete($committee->member_image);
        }
        
        $committee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Committee member deleted successfully',
        ]);
    }
}