<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RenewalController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function getExpiringMembers(Request $request)
    {
        $days = $request->get('days', 30);
        $members = Registration::getMembersExpiringInDays($days);
        
        $data = $members->map(function($member) {
            return [
                'id' => $member->id,
                'name' => $member->applicant_name,
                'member_id' => $member->member_id ?? $member->parent_member_id,
                'mobile' => $member->mobile,
                'email' => $member->office_email,
                'membership_plan' => $member->getRegistrationTypeDisplayAttribute(),
                'expiry_date' => $member->expiry_date->format('d M Y'),
                'days_left' => $member->days_left,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $data->count()
        ]);
    }

    public function sendRenewalReminder(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:registrations,id',
            'template_type' => 'required|in:30_days,8_days',
        ]);

        $member = Registration::find($validated['member_id']);
        
        if (!$member->mobile) {
            return response()->json([
                'success' => false,
                'message' => 'Member has no mobile number'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Map template types to your WABA template names
        $templateName = $this->getTemplateName($validated['template_type']);
        $parameters = $this->getTemplateParameters($member, $validated['template_type']);
        
        $result = $this->whatsappService->sendTemplateMessage(
            $member->mobile,
            $templateName,
            $parameters
        );
        
        if ($result['success']) {
            $this->logMessage($member, $templateName, 'sent');
            return response()->json([
                'success' => true,
                'message' => "Template sent to {$member->applicant_name}",
                'member' => $member->applicant_name
            ]);
        } else {
            $this->logMessage($member, $templateName, 'failed', $result['error']);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send: ' . ($result['error'] ?? 'Unknown error')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sendBulkReminders(Request $request)
    {
        $validated = $request->validate([
            'template_type' => 'required|in:30_days,8_days',
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:registrations,id',
        ]);

        $members = Registration::whereIn('id', $validated['member_ids'])
            ->whereNotNull('mobile')
            ->get();

        if ($members->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No members with mobile numbers found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $templateName = $this->getTemplateName($validated['template_type']);
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($members as $member) {
            $parameters = $this->getTemplateParameters($member, $validated['template_type']);
            $result = $this->whatsappService->sendTemplateMessage(
                $member->mobile,
                $templateName,
                $parameters
            );
            
            if ($result['success']) {
                $successCount++;
                $this->logMessage($member, $templateName, 'sent');
                $results[] = ['id' => $member->id, 'name' => $member->applicant_name, 'status' => 'sent'];
            } else {
                $failCount++;
                $this->logMessage($member, $templateName, 'failed', $result['error']);
                $results[] = ['id' => $member->id, 'name' => $member->applicant_name, 'status' => 'failed', 'error' => $result['error']];
            }
            
            usleep(500000); // 0.5 seconds delay
        }

        return response()->json([
            'success' => true,
            'message' => "Sent: {$successCount}, Failed: {$failCount}",
            'results' => $results
        ]);
    }

    /**
     * Get template name from WABA panel
     * Update these names to match your approved templates
     */
    private function getTemplateName($type)
    {
        $templates = [
            '30_days' => env('WHATSAPP_TEMPLATE_30_DAYS', 'membership_renewal_30_days'),
            '8_days' => env('WHATSAPP_TEMPLATE_8_DAYS', 'membership_renewal_8_days'),
        ];
        
        return $templates[$type];
    }

    /**
     * Get template parameters
     * Structure based on your WABA template
     */
    private function getTemplateParameters($member, $type)
    {
        $memberName = strtoupper($member->applicant_name);
        $memberId = $member->member_id ?? $member->parent_member_id;
        $expiryDate = $member->expiry_date->format('d F Y');
        $daysLeft = $member->days_left;
        
        // Adjust parameters based on your template structure
        // Your WABA template might have placeholders like {{1}}, {{2}}, etc.
        
        if ($type === '30_days') {
            return [
                'body' => [
                    $memberName,      // {{1}} - Member name
                    $memberId,        // {{2}} - Member ID
                    $expiryDate,      // {{3}} - Expiry date
                    $daysLeft,        // {{4}} - Days left
                ]
            ];
        } 
        
        elseif ($type === '8_days') {
            return [
                'body' => [
                    $memberName,      // {{1}} - Member name
                    $memberId,        // {{2}} - Member ID
                    $expiryDate,      // {{3}} - Expiry date
                    $daysLeft,        // {{4}} - Days left
                ],
                'buttons' => [
                    'Renew Now',      // Quick reply button text
                ]
            ];
        }
        
        return ['body' => [$memberName]];
    }

    private function logMessage($member, $templateName, $status, $error = null)
    {
        Log::channel('renewal_reminders')->info('WhatsApp template message', [
            'member_id' => $member->id,
            'member_name' => $member->applicant_name,
            'mobile' => $member->mobile,
            'template' => $templateName,
            'status' => $status,
            'error' => $error,
            'timestamp' => now()
        ]);
    }
}