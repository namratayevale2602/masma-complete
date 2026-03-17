<?php
// app/Http/Controllers/Api/AssociateController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Associate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AssociateController extends Controller
{
    public function index()
    {
        $associates = Associate::getActiveAssociates();
        
        return response()->json([
            'success' => true,
            'data' => $associates->map(function ($associate) {
                return [
                    'id' => $associate->id,
                    'name' => $associate->company_name,
                    'industry' => $associate->industry,
                    'description' => $associate->description,
                    'logo' => $associate->logo_url,
                    'order' => $associate->order,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['logo']);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_associate_' . $file->getClientOriginalName();
            $path = $file->storeAs('associates', $filename, 'uploads');
            $data['logo'] = $path;
        }

        if (!isset($data['order'])) {
            $data['order'] = Associate::max('order') + 1;
        }

        $associate = Associate::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Associate company created successfully',
            'data' => $associate,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $associate = Associate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'company_name' => 'sometimes|required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['logo']);

        if ($request->hasFile('logo')) {
            if ($associate->logo) {
                Storage::disk('uploads')->delete($associate->logo);
            }
            $file = $request->file('logo');
            $filename = time() . '_associate_' . $file->getClientOriginalName();
            $path = $file->storeAs('associates', $filename, 'uploads');
            $data['logo'] = $path;
        }

        $associate->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Associate company updated successfully',
            'data' => $associate,
        ]);
    }

    public function destroy($id)
    {
        $associate = Associate::findOrFail($id);
        
        if ($associate->logo) {
            Storage::disk('uploads')->delete($associate->logo);
        }
        
        $associate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Associate company deleted successfully',
        ]);
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'associates' => 'required|array',
            'associates.*.id' => 'required|exists:associates,id',
            'associates.*.order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->associates as $item) {
            Associate::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
    }
}