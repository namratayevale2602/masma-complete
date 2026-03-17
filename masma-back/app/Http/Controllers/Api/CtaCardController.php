<?php
// app/Http/Controllers/Api/CtaCardController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CtaCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CtaCardController extends Controller
{
    /**
     * Get all active cta cards
     */
    public function index()
    {
        $cards = CtaCard::getActiveCards();
        
        return response()->json([
            'success' => true,
            'data' => $cards->map(function ($card) {
                return [
                    'id' => $card->id,
                    'title' => $card->title,
                    'description' => $card->description,
                    'icon' => $card->icon,
                    'color' => $card->color,
                    'stats' => $card->stats,
                    'link' => $card->link,
                    'button_text' => $card->button_text,
                    'order' => $card->order,
                ];
            }),
        ]);
    }

    /**
     * Get a single cta card
     */
    public function show($id)
    {
        $card = CtaCard::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $card->id,
                'title' => $card->title,
                'description' => $card->description,
                'icon' => $card->icon,
                'color' => $card->color,
                'stats' => $card->stats,
                'link' => $card->link,
                'button_text' => $card->button_text,
                'order' => $card->order,
                'is_active' => $card->is_active,
            ],
        ]);
    }

    /**
     * Store a new cta card
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'stats' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        // Set default button text if not provided
        if (!isset($data['button_text'])) {
            $data['button_text'] = 'Register';
        }

        // Set order if not provided
        if (!isset($data['order'])) {
            $maxOrder = CtaCard::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        $card = CtaCard::create($data);

        return response()->json([
            'success' => true,
            'message' => 'CTA card created successfully',
            'data' => $card,
        ], 201);
    }

    /**
     * Update a cta card
     */
    public function update(Request $request, $id)
    {
        $card = CtaCard::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'stats' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $card->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'CTA card updated successfully',
            'data' => $card,
        ]);
    }

    /**
     * Delete a cta card
     */
    public function destroy($id)
    {
        $card = CtaCard::findOrFail($id);
        $card->delete();

        return response()->json([
            'success' => true,
            'message' => 'CTA card deleted successfully',
        ]);
    }

    /**
     * Update order of cta cards
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cards' => 'required|array',
            'cards.*.id' => 'required|exists:cta_cards,id',
            'cards.*.order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->cards as $item) {
            CtaCard::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
    }
}