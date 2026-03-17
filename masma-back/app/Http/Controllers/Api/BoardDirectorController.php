<?php
// app/Http/Controllers/Api/BoardDirectorController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoardDirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BoardDirectorController extends Controller
{
    /**
     * Get all active board directors
     */
    public function index(Request $request)
    {
        $year = $request->get('year', '2025-26');
        
        $directors = BoardDirector::getActiveDirectors($year);
        
        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'directors' => $directors->map(function ($director) {
                    return [
                        'id' => $director->id,
                        'name' => $director->name,
                        'place' => $director->place,
                        'designation' => $director->designation,
                        'education' => $director->education,
                        'experience' => $director->experience,
                        'image' => $director->image_url,
                        'order' => $director->order,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get a single board director
     */
    public function show($id)
    {
        $director = BoardDirector::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $director->id,
                'name' => $director->name,
                'place' => $director->place,
                'designation' => $director->designation,
                'education' => $director->education,
                'experience' => $director->experience,
                'image' => $director->image_url,
                'order' => $director->order,
                'is_active' => $director->is_active,
                'year' => $director->year,
            ],
        ]);
    }

    /**
     * Store a new board director
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'place' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order' => 'nullable|integer',
            'year' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except(['image']);

        // Set default year if not provided
        if (!isset($data['year'])) {
            $data['year'] = '2025-26';
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_director_' . $file->getClientOriginalName();
            $path = $file->storeAs('directors', $filename, 'uploads');
            $data['image'] = $path;
        }

        // Set order if not provided
        if (!isset($data['order'])) {
            $maxOrder = BoardDirector::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $director = BoardDirector::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Board director created successfully',
            'data' => $director,
        ], 201);
    }

    /**
     * Update a board director
     */
    public function update(Request $request, $id)
    {
        $director = BoardDirector::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'place' => 'sometimes|required|string|max:255',
            'designation' => 'sometimes|required|string|max:255',
            'education' => 'sometimes|required|string|max:255',
            'experience' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'year' => 'nullable|string|max:20',
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
            if ($director->image) {
                Storage::disk('uploads')->delete($director->image);
            }
            
            $file = $request->file('image');
            $filename = time() . '_director_' . $file->getClientOriginalName();
            $path = $file->storeAs('directors', $filename, 'uploads');
            $data['image'] = $path;
        }

        $director->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Board director updated successfully',
            'data' => $director,
        ]);
    }

    /**
     * Delete a board director
     */
    public function destroy($id)
    {
        $director = BoardDirector::findOrFail($id);

        // Delete image
        if ($director->image) {
            Storage::disk('uploads')->delete($director->image);
        }

        $director->delete();

        return response()->json([
            'success' => true,
            'message' => 'Board director deleted successfully',
        ]);
    }

    /**
     * Update order of directors
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'directors' => 'required|array',
            'directors.*.id' => 'required|exists:board_directors,id',
            'directors.*.order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->directors as $item) {
            BoardDirector::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
    }

    /**
     * Get available years
     */
    public function getYears()
    {
        $years = BoardDirector::distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return response()->json([
            'success' => true,
            'data' => $years,
        ]);
    }
}