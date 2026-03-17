<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Mail\RegistrationNotification;
use App\Mail\UserCredentials;
use App\Mail\PaymentVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    
    /**
     * Send WhatsApp template message
     */
    // private function sendWhatsAppTemplate($contact, $client_name)
    // {
    //     $apiUrl = 'https://partners.pinbot.ai/v1/messages';
    //     $apiKey = env('WHATSAPP_API_KEY');
    //     $phoneNumberId = env('WHATSAPP_PHONE_NUMBER');

    //     $data = [
    //         "messaging_product" => "whatsapp",
    //         "recipient_type" => "individual",
    //         "to" => $contact,
    //         "type" => "template",
    //         "template" => [
    //             "name" => "test", // Your template name
    //             "language" => ["code" => "en"],
    //             "components" => [
    //                 [
    //                     "type" => "body",
    //                     "parameters" => [
    //                         [
    //                             "type" => "text",
    //                             "text" => $client_name
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ];

    //     try {
    //         $curl = curl_init();
    //         curl_setopt_array($curl, [
    //             CURLOPT_URL => $apiUrl,
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_POST => true,
    //             CURLOPT_POSTFIELDS => json_encode($data),
    //             CURLOPT_HTTPHEADER => [
    //                 'Content-Type: application/json',
    //                 'apikey: ' . $apiKey,
    //                 'wanumber: ' . $phoneNumberId
    //             ],
    //             CURLOPT_TIMEOUT => 30,
    //             CURLOPT_SSL_VERIFYPEER => false // Only for development
    //         ]);

    //         $response = curl_exec($curl);
    //         $error = curl_error($curl);
    //         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //         curl_close($curl);

    //         if ($error) {
    //             Log::error('WhatsApp API Error: ' . $error);
    //             return ['success' => false, 'error' => $error];
    //         }

    //         $decoded = json_decode($response, true);
            
    //         if ($httpCode >= 400 || isset($decoded['error'])) {
    //             $errorMsg = $decoded['error']['message'] ?? 'Unknown WhatsApp API error';
    //             Log::error('WhatsApp API Response Error: ' . $errorMsg);
    //             return ['success' => false, 'error' => $errorMsg];
    //         }

    //         Log::info('WhatsApp message sent successfully to: ' . $contact);
    //         return ['success' => true, 'response' => $decoded];

    //     } catch (\Exception $e) {
    //         Log::error('WhatsApp Exception: ' . $e->getMessage());
    //         return ['success' => false, 'error' => $e->getMessage()];
    //     }
    // }

    /**
     * Send SMS message
     */
    private function sendSMS($contact)
    {
        $username = env('SMS_USERNAME');
        $password = env('SMS_PASSWORD');
        $senderId = env('SMS_SENDER_ID');

        $message = "FINAL CALL! Last chance to book a stall MASMA Renewable Energy Expo 2026 Stall booking closing soon. Call now 8999316256 or visit https://masmaexpo.in Team MASMA";

        $url = "https://www.smsjust.com/sms/user/urlsms.php?" . http_build_query([
            'username' => $username,
            'pass' => $password,
            'senderid' => $senderId,
            'dest_mobileno' => $contact,
            'msgtype' => 'TXT',
            'message' => $message,
            'response' => 'Y'
        ]);

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false // Only for development
            ]);

            $response = curl_exec($ch);
            
            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);
                Log::error('SMS API Error: ' . $error);
                return ['success' => false, 'error' => $error];
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('SMS sent successfully to: ' . $contact . ' | Response: ' . $response);
            return ['success' => true, 'response' => $response];

        } catch (\Exception $e) {
            Log::error('SMS Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send confirmation messages via WhatsApp and SMS
     */
    private function sendConfirmationMessages($registration)
    {
        $results = [
            'whatsapp' => null,
            'sms' => null
        ];

        // Send WhatsApp if mobile number exists
        // if (!empty($registration->mobile)) {
        //     $contact = $registration->mobile;
        //     // Remove any non-numeric characters and ensure it has country code
        //     $contact = preg_replace('/[^0-9]/', '', $contact);
        //     if (strlen($contact) === 10) {
        //         $contact = '91' . $contact; // Add India country code
        //     }
            
        //     $results['whatsapp'] = $this->sendWhatsAppTemplate($contact, $registration->applicant_name);
        // }

        // Send SMS
        if (!empty($registration->mobile)) {
            $results['sms'] = $this->sendSMS($registration->mobile, $registration->applicant_name);
        }

        return $results;
    }
    
    
      public function store(Request $request)
    {
        $validated = $request->validate([
            // Personal Information
            'applicant_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            
            // Contact Information
            'mobile' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'whatsapp_no' => 'nullable|string|max:20',
            'office_email' => 'required|email|max:255',
            
            // Address Information
            'city' => 'nullable|string|max:100',
            'town' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            
            // Business Information
            'organization' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'organization_type' => 'nullable|string|in:sole_proprietorship,partnership,limited_liability_partnership,private_limited_company,public_limited_company,one_person_company,other',
            'business_category' => 'nullable|string|in:student,plumber,electrician,installer_solar_pv,solar_water_heater,supplier,dealer,distributor,associate_member,manufacturer',
            'date_of_incorporation' => 'nullable|date',
            'pan_number' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:20',
            'about_service' => 'nullable|string',
            
            // Membership References
            'membership_reference_1' => 'required|string|max:255',
            'membership_reference_2' => 'required|string|max:255',
            
            // Registration Details
            'registration_type' => 'required|string|in:renew_epc_classic,student,installer,epc_classic,dealer_distributor,silver_corporate,gold_corporate',
            'registration_amount' => 'required|numeric|min:0',
            
            // Payment Details - NOW REQUIRED
            'payment_mode' => 'required|string|in:neft,upi,rtgs,imps,cash,cheque',
            'transaction_reference' => 'required|string|max:255',
            
            // Files
            'applicant_photo' => 'nullable|image|max:5120',
            'visiting_card' => 'nullable|image|max:5120',
            'payment_screenshot' => 'required|image|max:5120',
            
            // Declaration
            'declaration' => 'required|boolean',
        ], [
            'office_email.required' => 'Email address is required.',
            'payment_mode.required' => 'Please select a payment mode.',
            'transaction_reference.required' => 'Please enter the transaction reference number.',
            'payment_screenshot.required' => 'Please upload a payment screenshot.',
            'payment_screenshot.image' => 'Payment screenshot must be an image file.',
            'payment_screenshot.max' => 'Payment screenshot must not exceed 5MB.',
        ]);

        try {
            DB::beginTransaction();

            // Handle different boolean formats for declaration
            $declaration = $request->declaration;
            if (is_string($declaration)) {
                $declaration = filter_var($declaration, FILTER_VALIDATE_BOOLEAN);
            }
            $validated['declaration'] = (bool)$declaration;

            // Set payment_verified to false initially
            $validated['payment_verified'] = false;

            // Handle file uploads
            if ($request->hasFile('applicant_photo')) {
                $photoPath = $request->file('applicant_photo')->store('applicant-photos', 'public');
                $validated['applicant_photo_path'] = $photoPath;
            }

            if ($request->hasFile('visiting_card')) {
                $visitingCardPath = $request->file('visiting_card')->store('visiting-cards', 'public');
                $validated['visiting_card_path'] = $visitingCardPath;
            }

            if ($request->hasFile('payment_screenshot')) {
                $screenshotPath = $request->file('payment_screenshot')->store('payment-screenshots', 'public');
                $validated['payment_screenshot_path'] = $screenshotPath;
            }

            // Remove file fields from validated data
            unset($validated['applicant_photo']);
            unset($validated['visiting_card']);
            unset($validated['payment_screenshot']);

            $registration = Registration::create($validated);

            DB::commit();

            // Send email notification to admin
            try {
                $adminEmail = config('mail.from.address');
                Mail::to($adminEmail)->send(new RegistrationNotification($registration, $adminEmail));
                Log::info('Registration notification email sent to admin: ' . $adminEmail);
            } catch (\Exception $e) {
                Log::error('Failed to send registration notification email: ' . $e->getMessage());
            }

            // Send WhatsApp and SMS confirmation to user
            $messageResults = $this->sendConfirmationMessages($registration);
            
            // Log message results
            if ($messageResults['whatsapp']) {
                if ($messageResults['whatsapp']['success']) {
                    Log::info('WhatsApp confirmation sent to: ' . $registration->mobile);
                } else {
                    Log::warning('WhatsApp failed: ' . ($messageResults['whatsapp']['error'] ?? 'Unknown error'));
                }
            }
            
            if ($messageResults['sms']) {
                if ($messageResults['sms']['success']) {
                    Log::info('SMS confirmation sent to: ' . $registration->mobile);
                } else {
                    Log::warning('SMS failed: ' . ($messageResults['sms']['error'] ?? 'Unknown error'));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration submitted successfully! Your payment details have been received and are pending verification.',
                'data' => $registration,
                'notifications' => [
                    'whatsapp' => $messageResults['whatsapp'] ?? null,
                    'sms' => $messageResults['sms'] ?? null
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit registration. Please try again.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Verify payment (admin function)
    public function verifyPayment(Request $request, Registration $registration)
    {
        $request->validate([
            'payment_verified' => 'required|boolean',
            'payment_remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $registration->payment_verified;
            
            $registration->update([
                'payment_verified' => $request->payment_verified,
                'payment_verified_at' => $request->payment_verified ? now() : null,
                'payment_remarks' => $request->payment_remarks,
            ]);

            DB::commit();

            // Send email notification to user about payment verification
            try {
                Mail::to($registration->office_email)
                    ->send(new PaymentVerificationNotification($registration, $request->payment_verified));
                    
                \Log::info('Payment verification email sent to: ' . $registration->office_email);
            } catch (\Exception $e) {
                \Log::error('Failed to send payment verification email: ' . $e->getMessage());
            }

            $status = $request->payment_verified ? 'verified' : 'rejected';
            
            return response()->json([
                'success' => true,
                'message' => "Payment {$status} successfully.",
                'data' => $registration
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment verification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment. Please try again.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Get pending payments (admin function)
    public function getPendingPayments()
    {
        $pendingRegistrations = Registration::where('payment_verified', false)
            ->whereNotNull('payment_screenshot_path')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pendingRegistrations
        ]);
    }

    // Get payment statistics (admin function)
    public function getPaymentStats()
    {
        $stats = [
            'total_registrations' => Registration::count(),
            'pending_payments' => Registration::where('payment_verified', false)
                ->whereNotNull('payment_screenshot_path')
                ->count(),
            'verified_payments' => Registration::where('payment_verified', true)->count(),
            'total_amount_collected' => Registration::where('payment_verified', true)
                ->sum('registration_amount'),
            'payments_by_mode' => Registration::where('payment_verified', true)
                ->select('payment_mode', DB::raw('count(*) as count'), DB::raw('sum(registration_amount) as total'))
                ->groupBy('payment_mode')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function sendCredentials(Registration $registration)
    {
        if (!$registration->payment_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Payment must be verified before sending credentials.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($registration->credentials_sent) {
            return response()->json([
                'success' => false,
                'message' => 'Credentials have already been sent to this user.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Generate secure password
        $plainPassword = Registration::generateNewPassword();
        
        // Hash the password for secure storage
        $hashedPassword = Registration::hashPassword($plainPassword);
        
        // Update registration with hashed password
        $registration->update([
            'generated_password' => $hashedPassword,
            'credentials_sent' => true,
            'credentials_sent_at' => now(),
        ]);

        // Send credentials email with plaintext password
        try {
            Mail::to($registration->office_email)
                ->send(new UserCredentials($registration, $plainPassword));
                
            \Log::info('Credentials email sent to: ' . $registration->office_email);
                
            return response()->json([
                'success' => true,
                'message' => 'Credentials sent successfully to ' . $registration->office_email,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send credentials email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function resetPassword(Registration $registration)
    {
        // Generate new secure password
        $newPlainPassword = Registration::generateNewPassword();
        $newHashedPassword = Registration::hashPassword($newPlainPassword);
        
        // Update the password in database
        $registration->update([
            'generated_password' => $newHashedPassword,
            'credentials_sent' => true,
            'credentials_sent_at' => now(),
        ]);

        // Send email with new password
        try {
            Mail::to($registration->office_email)
                ->send(new UserCredentials($registration, $newPlainPassword, true));
                
            \Log::info('Password reset email sent to: ' . $registration->office_email);
                
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. New credentials sent to ' . $registration->office_email,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $registration = Registration::where('office_email', $request->email)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$registration->payment_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Your payment is pending verification. Please wait for admin approval.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$registration->generated_password) {
            return response()->json([
                'success' => false,
                'message' => 'No password set for this account. Please contact admin.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($request->password, $registration->generated_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => $registration
        ]);
    }

    public function show(Registration $registration)
    {
        return response()->json([
            'success' => true,
            'data' => $registration
        ]);
    }

    public function index(Request $request)
    {
        $query = Registration::query();

        // Filter by payment status
        if ($request->has('payment_status')) {
            if ($request->payment_status === 'verified') {
                $query->where('payment_verified', true);
            } elseif ($request->payment_status === 'pending') {
                $query->where('payment_verified', false);
            }
        }

        // Filter by registration type
        if ($request->has('registration_type')) {
            $query->where('registration_type', $request->registration_type);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                  ->orWhere('office_email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $registrations
        ]);
    }

    public function update(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'applicant_name' => 'sometimes|required|string|max:255',
            'date_of_birth' => 'sometimes|required|date',
            'organization' => 'nullable|string|max:255',
            'mobile' => 'sometimes|required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'whatsapp_no' => 'nullable|string|max:20',
            'office_email' => 'sometimes|required|email|max:255' . $registration->id,
            'city' => 'nullable|string|max:100',
            'town' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'organization_type' => 'nullable|string',
            'business_category' => 'nullable|string',
            'date_of_incorporation' => 'nullable|date',
            'pan_number' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:20',
            'about_service' => 'nullable|string',
            'membership_reference_1' => 'sometimes|required|string|max:255',
            'membership_reference_2' => 'sometimes|required|string|max:255',
            'registration_type' => 'sometimes|required|string',
            'registration_amount' => 'sometimes|required|numeric',
            'payment_mode' => 'nullable|string',
            'transaction_reference' => 'nullable|string|max:255',
            'declaration' => 'sometimes|required|boolean',
            'payment_verified' => 'sometimes|boolean',
            'applicant_photo' => 'nullable|image|max:5120',
            'visiting_card' => 'nullable|image|max:5120',
            'payment_screenshot' => 'nullable|image|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            if ($request->hasFile('applicant_photo')) {
                if ($registration->applicant_photo_path) {
                    Storage::disk('public')->delete($registration->applicant_photo_path);
                }
                $photoPath = $request->file('applicant_photo')->store('applicant-photos', 'public');
                $validated['applicant_photo_path'] = $photoPath;
            }

            if ($request->hasFile('visiting_card')) {
                if ($registration->visiting_card_path) {
                    Storage::disk('public')->delete($registration->visiting_card_path);
                }
                $visitingCardPath = $request->file('visiting_card')->store('visiting-cards', 'public');
                $validated['visiting_card_path'] = $visitingCardPath;
            }

            if ($request->hasFile('payment_screenshot')) {
                if ($registration->payment_screenshot_path) {
                    Storage::disk('public')->delete($registration->payment_screenshot_path);
                }
                $screenshotPath = $request->file('payment_screenshot')->store('payment-screenshots', 'public');
                $validated['payment_screenshot_path'] = $screenshotPath;
            }

            // Remove file fields from validated data
            unset($validated['applicant_photo']);
            unset($validated['visiting_card']);
            unset($validated['payment_screenshot']);

            $registration->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration updated successfully.',
                'data' => $registration
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update registration.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Registration $registration)
    {
        try {
            DB::beginTransaction();

            // Delete associated files
            if ($registration->applicant_photo_path) {
                Storage::disk('public')->delete($registration->applicant_photo_path);
            }
            if ($registration->visiting_card_path) {
                Storage::disk('public')->delete($registration->visiting_card_path);
            }
            if ($registration->payment_screenshot_path) {
                Storage::disk('public')->delete($registration->payment_screenshot_path);
            }

            $registration->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration deleted successfully.'
            ], Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Delete error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete registration.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}