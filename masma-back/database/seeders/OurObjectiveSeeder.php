<?php
// database/seeders/OurObjectiveSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Objective;
use App\Models\DirectorResponsibility;
use App\Models\EthicalStandard;
use App\Models\VisionMissionGoal;

class OurObjectiveSeeder extends Seeder
{
    public function run(): void
    {
        // Vision
        VisionMissionGoal::create([
            'type' => 'vision',
            'title' => 'Our Vision',
            'description' => 'To establish Maharashtra as the leading solar energy hub in India, driving sustainable development and energy independence through widespread adoption of solar technologies.',
            'icon' => 'FaEye',
            'items' => [
                "Make proper organisation structure and system for MASMA",
                "Finical empowering to regional team",
                "Solar thermal cell",
                "Nothing else only business!!",
                "Tender cell",
                "Launching \"Surya veer\" yojan",
                "Promote MASMA till end customer",
                "Quarterly meeting",
                "Regional activity like tech series, factory visit etc.",
                "Training of members about quality.",
            ],
            'order' => 1,
            'is_active' => true,
        ]);

        // Mission
        VisionMissionGoal::create([
            'type' => 'mission',
            'title' => 'Our Mission',
            'description' => 'To unite, strengthen, and empower the solar industry in Maharashtra through collaboration, education, and advocacy while promoting ethical practices and sustainable growth.',
            'icon' => 'FaBullseye',
            'items' => [
                ['text' => 'MNRE & SECI central govt authorities.'],
                ['text' => 'MEDA Head office & Regional offices.'],
                ['text' => 'MSEDCL Local & Regional offices.'],
                ['text' => 'Municipal Corporations.'],
                ['text' => 'MASMA membership above 500 Members PAN Maharashtra.'],
                ['text' => 'Recognition of MASMA activities by Central & State Authorities.'],
                ['text' => 'Active role of MASMA in making policy decisions with government authorities.'],
                ['text' => 'Formation of the task force to interact with MEDA & MSEDCL to have the easy implementation of Solar rooftop programmers.'],
                ['text' => 'MASMA\'s own offices & infrastructure.'],
                ['text' => 'Interaction with Govt. Authorities for a favorable & reasonable subsidy policy for end users.'],
                ['text' => 'Provide knowledge, business, and employment.'],
                ['text' => 'Draft quality specifications as per government guidelines & quality standards.'],
                ['text' => 'To set benchmark prices for solar systems as per the MNRE guidelines.'],
            ],
            'order' => 2,
            'is_active' => true,
        ]);

        // Goals
        $goals = [
            [
                'title' => 'Strong advisor committee.',
            ],
            [
                'title' => 'Collaboration with other organisation',
            ],
            [
                'title' => 'Solve different policy issue.',
            ],
            [
                'title' => 'Educate member\'s for equal purchase price and controlling cost of BOM.',
            ],
            [
                'title' => 'Educate member\'s on manufacture\'s warranty terms.',
            ],
            [
                'title' => 'Masma help line desk with number',
            ],
            [
                'title' => 'Organizing and heading all India solar federation ( Federation of Renewable Association of India)',
            ],
        ];

        foreach ($goals as $index => $goal) {
            VisionMissionGoal::create([
                'type' => 'goal',
                'title' => 'Our Goals',
                'description' => 'Strategic objectives to achieve our vision and fulfill our mission through focused initiatives and measurable outcomes.',
                'icon' => 'FaFlag',
                'items' => [$goal],
                'order' => $index + 3,
                'is_active' => true,
            ]);
        }

        // Objectives
        $objectives = [
            [
                'title' => 'Industry Unification',
                'description' => 'Uniting all manufacturers, installers, and dealers engaged in Solar Hot Water and Solar System in Maharashtra.',
                'icon' => 'FaUsers',
                'order' => 1,
            ],
            [
                'title' => 'Government Coordination',
                'description' => 'Interacting with Central Ministry MNRE, State ministry, and State Nodal Agencies for public awareness.',
                'icon' => 'FaHandshake',
                'order' => 2,
            ],
            [
                'title' => 'Banking Partnerships',
                'description' => 'Working with nationalized and cooperative banks involved in interest subsidy schemes.',
                'icon' => 'FaMoneyCheck',
                'order' => 3,
            ],
            [
                'title' => 'Training Programs',
                'description' => 'Conducting training for installers, plumbers, marketers in coordination with industry associations.',
                'icon' => 'FaGraduationCap',
                'order' => 4,
            ],
            [
                'title' => 'Product Awareness',
                'description' => 'Organizing webinars and programs for new product launches and solar system awareness.',
                'icon' => 'FaBullhorn',
                'order' => 5,
            ],
            [
                'title' => 'Comprehensive Support',
                'description' => 'Providing technical support, training, recruitment, and policy guidance across the solar value chain.',
                'icon' => 'FaCog',
                'order' => 6,
            ],
        ];

        foreach ($objectives as $objective) {
            Objective::create($objective);
        }

        // Director Responsibilities
        $responsibilities = [
            ['task' => 'Broadening the number of MASMA members', 'icon' => 'FaUserTie', 'order' => 1],
            ['task' => 'Conduct monthly meetings', 'icon' => 'FaClipboardCheck', 'order' => 2],
            ['task' => 'Interact with MEDA, MSEDCL, and Government officials', 'icon' => 'FaBuilding', 'order' => 3],
            ['task' => 'Conduct sponsored knowledge series lectures quarterly', 'icon' => 'FaChartLine', 'order' => 4],
            ['task' => 'Organize Solar Exhibitions at Regional levels', 'icon' => 'FaSun', 'order' => 5],
            ['task' => 'Training of Manpower for Solar Sector', 'icon' => 'FaGraduationCap', 'order' => 6],
            ['task' => 'Take prior sanction for major expenses from HO', 'icon' => 'FaMoneyCheck', 'order' => 7],
            ['task' => 'Send expenses and meeting minutes to HO monthly', 'icon' => 'FaClipboardCheck', 'order' => 8],
            ['task' => 'Resolve members grievances locally under HO guidance', 'icon' => 'FaShieldAlt', 'order' => 9],
        ];

        foreach ($responsibilities as $responsibility) {
            DirectorResponsibility::create($responsibility);
        }

        // Ethical Standards
        $standards = [
            [
                'title' => 'Organizational Representation',
                'description' => 'Every member should realize that he represents MASMA as an Organization.',
                'icon' => 'FaAward',
                'order' => 1,
            ],
            [
                'title' => 'Transparency',
                'description' => 'Member should be transparent about the technology offering to customers.',
                'icon' => 'FaShieldAlt',
                'order' => 2,
            ],
            [
                'title' => 'Fair Pricing & Quality',
                'description' => 'Reasonable pricing & quality standards.',
                'icon' => 'FaBalanceScale',
                'order' => 3,
            ],
            [
                'title' => 'Ethical Competition',
                'description' => 'Ethical practices among business competitors should be maintained.',
                'icon' => 'FaHandshake',
                'order' => 4,
            ],
        ];

        foreach ($standards as $standard) {
            EthicalStandard::create($standard);
        }
    }
}