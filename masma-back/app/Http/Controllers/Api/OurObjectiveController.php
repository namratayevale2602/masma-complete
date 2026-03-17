<?php
// app/Http/Controllers/Api/OurObjectiveController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Objective;
use App\Models\DirectorResponsibility;
use App\Models\EthicalStandard;
use App\Models\VisionMissionGoal;
use Illuminate\Http\Request;

class OurObjectiveController extends Controller
{
    /**
     * Get all data for Our Objective page
     */
    public function index()
    {
        $vision = VisionMissionGoal::getVision();
        $mission = VisionMissionGoal::getMission();
        $goals = VisionMissionGoal::getGoals();
        $objectives = Objective::getActiveObjectives();
        $responsibilities = DirectorResponsibility::getActiveResponsibilities();
        $standards = EthicalStandard::getActiveStandards();

        return response()->json([
            'success' => true,
            'data' => [
                'page_title' => 'Our Guiding Principles',
                'page_subtitle' => 'MASMA (The Maharashtra Solar Manufactures\' Association) - Driving solar energy adoption through unity, training, and ethical practices.',
                'vision' => $vision ? [
                    'id' => $vision->id,
                    'title' => $vision->title,
                    'description' => $vision->description,
                    'icon' => $vision->icon,
                    'highlights' => $vision->items ?? [],
                ] : null,
                'mission' => $mission ? [
                    'id' => $mission->id,
                    'title' => $mission->title,
                    'description' => $mission->description,
                    'icon' => $mission->icon,
                    'points' => $mission->items ?? [],
                ] : null,
                'goals' => $goals->map(function($goal) {
                    return [
                        'id' => $goal->id,
                        'title' => $goal->title,
                        'description' => $goal->description,
                        'icon' => $goal->icon,
                        'categories' => $goal->items ?? [],
                    ];
                }),
                'objectives' => $objectives->map(function($objective) {
                    return [
                        'id' => $objective->id,
                        'title' => $objective->title,
                        'description' => $objective->description,
                        'icon' => $objective->icon,
                    ];
                }),
                'director_responsibilities' => $responsibilities->map(function($responsibility) {
                    return [
                        'id' => $responsibility->id,
                        'task' => $responsibility->task,
                        'icon' => $responsibility->icon,
                    ];
                }),
                'ethical_standards' => $standards->map(function($standard) {
                    return [
                        'id' => $standard->id,
                        'title' => $standard->title,
                        'description' => $standard->description,
                        'icon' => $standard->icon,
                    ];
                }),
            ],
        ]);
    }
}