<?php
// app/Http/Controllers/Api/StatController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatController extends Controller
{
    /**
     * Get all active stats
     */
    public function index()
    {
        $stats = Stat::getActiveStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats->map(function ($stat) {
                return [
                    'id' => $stat->id,
                    'value' => $stat->value,
                    'label' => $stat->label,
                    'icon' => $stat->icon,
                    'order' => $stat->order,
                ];
            }),
        ]);
    }

    /**
     * Get a single stat
     */
    public function show($id)
    {
        $stat = Stat::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $stat->id,
                'value' => $stat->value,
                'label' => $stat->label,
                'icon' => $stat->icon,
                'order' => $stat->order,
                'is_active' => $stat->is_active,
            ],
        ]);
    }

    /**
     * Store a new stat
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'value' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        // Set order if not provided
        if (!isset($data['order'])) {
            $maxOrder = Stat::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $stat = Stat::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Stat created successfully',
            'data' => $stat,
        ], 201);
    }

    /**
     * Update a stat
     */
    public function update(Request $request, $id)
    {
        $stat = Stat::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'label' => 'sometimes|required|string|max:255',
            'value' => 'sometimes|required|integer|min:0',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $stat->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Stat updated successfully',
            'data' => $stat,
        ]);
    }

    /**
     * Delete a stat
     */
    public function destroy($id)
    {
        $stat = Stat::findOrFail($id);
        $stat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stat deleted successfully',
        ]);
    }

    /**
     * Update order of stats
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stats' => 'required|array',
            'stats.*.id' => 'required|exists:stats,id',
            'stats.*.order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->stats as $item) {
            Stat::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
    }
}