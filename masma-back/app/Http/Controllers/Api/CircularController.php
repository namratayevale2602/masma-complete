<?php
// app/Http/Controllers/Api/CircularController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Circular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CircularController extends Controller
{
    public function index()
    {
        $groupedCirculars = Circular::getGroupedByCategory();
        
        $formattedData = [];
        
        foreach ($groupedCirculars as $category => $circulars) {
            $section = [
                'id' => $circulars->first()->id,
                'title' => $category,
                'subtitle' => $circulars->first()->subcategory ?? 'Documents',
                'icon' => $category === 'Important Circular' ? 'FaClipboardList' : 'FaFileAlt',
                'items' => $circulars->map(function ($circular) {
                    return [
                        'id' => $circular->id,
                        'title' => $circular->title,
                        'description' => $circular->description,
                        'type' => $circular->file_type,
                        'link' => $circular->file_url,
                    ];
                })->values(),
            ];
            
            $formattedData[] = $section;
        }

        return response()->json([
            'success' => true,
            'data' => $formattedData,
        ]);
    }

    public function show($id)
    {
        $circular = Circular::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $circular->id,
                'title' => $circular->title,
                'description' => $circular->description,
                'file_url' => $circular->file_url,
                'file_type' => $circular->file_type,
                'category' => $circular->category,
                'subcategory' => $circular->subcategory,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'category' => 'required|string|max:255',
            'subcategory' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['file']);
        $data['file_type'] = 'pdf';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $request->title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('circulars', $filename, 'uploads');
            $data['file_path'] = $path;
        }

        if (!isset($data['order'])) {
            $data['order'] = Circular::max('order') + 1;
        }

        $circular = Circular::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Circular created successfully',
            'data' => $circular,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $circular = Circular::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'category' => 'sometimes|required|string|max:255',
            'subcategory' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['file']);

        if ($request->hasFile('file')) {
            // Delete old file
            if ($circular->file_path) {
                Storage::disk('uploads')->delete($circular->file_path);
            }
            
            $file = $request->file('file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $request->title ?? $circular->title) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('circulars', $filename, 'uploads');
            $data['file_path'] = $path;
            $data['file_type'] = 'pdf';
        }

        $circular->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Circular updated successfully',
            'data' => $circular,
        ]);
    }

    public function destroy($id)
    {
        $circular = Circular::findOrFail($id);
        
        // Delete file
        if ($circular->file_path) {
            Storage::disk('uploads')->delete($circular->file_path);
        }
        
        $circular->delete();

        return response()->json([
            'success' => true,
            'message' => 'Circular deleted successfully',
        ]);
    }
}