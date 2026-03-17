<?php
// app/Http/Controllers/Api/MembershipFeatureController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MembershipFeatureController extends Controller
{
    public function index()
    {
        $features = MembershipFeature::getActiveFeatures();

        return response()->json([
            'success' => true,
            'data' => $features->map(function ($feature) {
                return [
                    'id' => $feature->id,
                    'label' => $feature->label,
                    'key' => $feature->key,
                    'description' => $feature->description,
                    'order' => $feature->order,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'key' => 'required|string|max:100|unique:membership_features',
            'description' => 'nullable|string',
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
            $data['order'] = MembershipFeature::max('order') + 1;
        }

        $feature = MembershipFeature::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Feature created successfully',
            'data' => $feature,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $feature = MembershipFeature::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'label' => 'sometimes|required|string|max:255',
            'key' => 'sometimes|required|string|max:100|unique:membership_features,key,' . $id,
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $feature->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Feature updated successfully',
            'data' => $feature,
        ]);
    }

    public function destroy($id)
    {
        $feature = MembershipFeature::findOrFail($id);
        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feature deleted successfully',
        ]);
    }
}