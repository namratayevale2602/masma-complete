<?php
// app/Http/Controllers/Api/ParticipantController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ParticipantController extends Controller
{
    /**
     * Get all active participants grouped by row
     */
    public function index()
    {
        $participants = Participant::getParticipantsByRow();
        
        return response()->json([
            'success' => true,
            'data' => [
                'row1' => $participants['row1']->map(function ($participant) {
                    return [
                        'id' => $participant->id,
                        'image' => $participant->image_url,
                        'alt_text' => $participant->alt_text,
                        'row' => $participant->row,
                        'order' => $participant->order,
                    ];
                }),
                'row2' => $participants['row2']->map(function ($participant) {
                    return [
                        'id' => $participant->id,
                        'image' => $participant->image_url,
                        'alt_text' => $participant->alt_text,
                        'row' => $participant->row,
                        'order' => $participant->order,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Store a newly created participant
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
        'row' => 'required|integer|in:1,2',
        'order' => 'nullable|integer',
        'alt_text' => 'nullable|string|max:255',
        'is_active' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $data = $request->except(['image']);

    // Handle image upload - store original without modification
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        // Preserve original filename with timestamp
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('participants', $filename, 'uploads');
        $data['image'] = $path;
    }

    $participant = Participant::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Participant created successfully',
        'data' => $participant,
    ], 201);
}

    /**
     * Display the specified participant
     */
    public function show($id)
    {
        $participant = Participant::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $participant->id,
                'image' => $participant->image_url,
                'alt_text' => $participant->alt_text,
                'row' => $participant->row,
                'order' => $participant->order,
                'is_active' => $participant->is_active,
            ],
        ]);
    }

    /**
     * Update the specified participant
     */
   public function update(Request $request, $id)
{
    $participant = Participant::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'row' => 'nullable|integer|in:1,2',
        'order' => 'nullable|integer',
        'alt_text' => 'nullable|string|max:255',
        'is_active' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $data = $request->except(['image']);

    // Handle image upload - store original without modification
    if ($request->hasFile('image')) {
        // Delete old image
        if ($participant->image) {
            Storage::disk('uploads')->delete($participant->image);
        }
        
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('participants', $filename, 'uploads');
        $data['image'] = $path;
    }

    $participant->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Participant updated successfully',
        'data' => $participant,
    ]);
}

    /**
     * Remove the specified participant
     */
    public function destroy($id)
    {
        $participant = Participant::findOrFail($id);

        // Delete image
        if ($participant->image) {
            Storage::disk('uploads')->delete($participant->image);
        }

        $participant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Participant deleted successfully',
        ]);
    }

    /**
     * Update the order of participants
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:participants,id',
            'orders.*.order' => 'required|integer',
            'orders.*.row' => 'required|integer|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->orders as $item) {
            Participant::where('id', $item['id'])->update([
                'order' => $item['order'],
                'row' => $item['row'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
    }

    /**
     * Bulk upload participants
     */
   public function bulkUpload(Request $request)
{
    $validator = Validator::make($request->all(), [
        'participants' => 'required|array',
        'participants.*.title' => 'nullable|string|max:255',
        'participants.*.description' => 'nullable|string',
        'participants.*.image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'participants.*.row' => 'required|integer|in:1,2',
        'participants.*.alt_text' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $createdParticipants = [];

    foreach ($request->file('participants') as $index => $participantFile) {
        $participantData = $request->input('participants.' . $index);
        
        // Store image with original filename
        $filename = time() . '_' . $participantFile->getClientOriginalName();
        $path = $participantFile->storeAs('participants', $filename, 'uploads');
        
        // Get max order for the row
        $maxOrder = Participant::where('row', $participantData['row'])->max('order') ?? 0;
        
        $participant = Participant::create([
            'title' => $participantData['title'] ?? null,
            'description' => $participantData['description'] ?? null,
            'image' => $path,
            'row' => $participantData['row'],
            'order' => $maxOrder + 1,
            'alt_text' => $participantData['alt_text'] ?? null,
            'is_active' => true,
        ]);
        
        $createdParticipants[] = $participant;
    }

    return response()->json([
        'success' => true,
        'message' => count($createdParticipants) . ' participants uploaded successfully',
        'data' => $createdParticipants,
    ], 201);
}
}