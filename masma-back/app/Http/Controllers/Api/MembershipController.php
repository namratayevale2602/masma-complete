<?php
// app/Http/Controllers/Api/MembershipController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\MembershipFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MembershipController extends Controller
{
    /**
     * Get all membership data
     */
    public function index()
    {
        $plans = MembershipPlan::getActivePlans();
        $features = MembershipFeature::getActiveFeatures();

        return response()->json([
            'success' => true,
            'data' => [
                'membershipPlans' => $plans->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'type' => $plan->type,
                        'pricing' => [
                            'membershipFee' => $plan->membership_fee,
                            'registrationCharges' => $plan->registration_charges,
                            'duration' => $plan->duration,
                        ],
                        'features' => $plan->features ?? [],
                        'highlight' => $plan->is_highlighted,
                        'order' => $plan->order,
                    ];
                }),
                'featureLabels' => $features->pluck('label'),
            ],
        ]);
    }

    /**
     * Get a single membership plan
     */
    public function show($id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $features = MembershipFeature::getActiveFeatures();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'type' => $plan->type,
                'pricing' => [
                    'membershipFee' => $plan->membership_fee,
                    'registrationCharges' => $plan->registration_charges,
                    'duration' => $plan->duration,
                ],
                'features' => $plan->features ?? [],
                'featureLabels' => $features->pluck('label'),
                'highlight' => $plan->is_highlighted,
                'order' => $plan->order,
                'is_active' => $plan->is_active,
            ],
        ]);
    }

    /**
     * Store a new membership plan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'membership_fee' => 'required|string|max:50',
            'registration_charges' => 'nullable|string|max:50',
            'duration' => 'required|string|max:100',
            'features' => 'nullable|array',
            'order' => 'nullable|integer',
            'is_highlighted' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        if (!isset($data['order'])) {
            $data['order'] = MembershipPlan::max('order') + 1;
        }

        $plan = MembershipPlan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Membership plan created successfully',
            'data' => $plan,
        ], 201);
    }

    /**
     * Update a membership plan
     */
    public function update(Request $request, $id)
    {
        $plan = MembershipPlan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'nullable|string|max:100',
            'membership_fee' => 'sometimes|required|string|max:50',
            'registration_charges' => 'nullable|string|max:50',
            'duration' => 'sometimes|required|string|max:100',
            'features' => 'nullable|array',
            'order' => 'nullable|integer',
            'is_highlighted' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $plan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Membership plan updated successfully',
            'data' => $plan,
        ]);
    }

    /**
     * Delete a membership plan
     */
    public function destroy($id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Membership plan deleted successfully',
        ]);
    }

    /**
     * Update order of plans
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plans' => 'required|array',
            'plans.*.id' => 'required|exists:membership_plans,id',
            'plans.*.order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->plans as $item) {
            MembershipPlan::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
        ]);
    }
}