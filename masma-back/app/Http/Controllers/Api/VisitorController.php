<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Mail\VisitorQrCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VisitorController extends Controller
{
    /**
     * Store a newly created visitor (WITHOUT QR CODE GENERATION)
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'visitor_name' => 'required|string|max:255',
            'bussiness_name' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'whatsapp_no' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'city' => 'nullable|string|max:100',
            'town' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'remark' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create visitor WITHOUT QR code
            $visitor = Visitor::create($validator->validated());

            // Generate the scan URL (no QR code generation here)
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            $scanUrl = $frontendUrl . '/visitor/' . $visitor->id . '/card';
            
            // Prepare response WITHOUT QR code
            $response = [
                'success' => true,
                'message' => 'Visitor registered successfully',
                'visitor' => [
                    'id' => $visitor->id,
                    'name' => $visitor->visitor_name,
                    'email' => $visitor->email,
                ],
                'scan_url' => $scanUrl,
                'email_status' => 'pending', // Will be updated when QR code is sent
            ];

            return response()->json($response, 201);

        } catch (\Exception $e) {
            Log::error('Visitor registration error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to register visitor. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store QR code generated from frontend
     */
    public function storeQrCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visitor_id' => 'required|integer|exists:visitors,id',
            'qr_code_data' => 'required|string', // base64 image data
            'qr_code_metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $visitor = Visitor::findOrFail($request->visitor_id);
            
            // Decode base64 image (remove data:image/png;base64, prefix if present)
            $base64Image = $request->qr_code_data;
            
            // Check if it has data URI prefix
            if (strpos($base64Image, 'data:image') === 0) {
                // Extract just the base64 part
                $parts = explode(',', $base64Image);
                $base64Image = $parts[1] ?? $base64Image;
            }
            
            $imageData = base64_decode($base64Image);
            
            if (!$imageData) {
                throw new \Exception('Invalid base64 image data');
            }

            // Save QR code to storage
            $directory = 'public/qr-codes';
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            $fileName = 'qr-codes/visitor-' . $visitor->id . '-' . time() . '.png';
            Storage::disk('public')->put($fileName, $imageData);

            // Prepare QR code data
            $qrData = [
                'id' => $visitor->id,
                'name' => $visitor->visitor_name,
                'email' => $visitor->email,
                'mobile' => $visitor->mobile,
                'business' => $visitor->bussiness_name,
                'date' => $visitor->created_at->format('Y-m-d H:i:s'),
                'type' => 'visitor',
                'generated_at' => now()->toISOString(),
                'generated_by' => 'frontend',
            ];

            // Merge with metadata if provided
            if ($request->has('qr_code_metadata')) {
                $qrData = array_merge($qrData, $request->qr_code_metadata);
            }

            // Update visitor with QR code info
            $visitor->update([
                'qr_code_path' => $fileName,
                'qr_code_data' => json_encode($qrData),
            ]);

            // Refresh visitor
            $visitor->refresh();

            // Send email with QR code
            $emailStatus = 'not_sent';
            try {
                $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
                $cardUrl = $frontendUrl . '/visitor/' . $visitor->id . '/card';
                
                Mail::to($visitor->email)->send(new VisitorQrCodeMail($visitor, $cardUrl));
                $emailStatus = 'sent';
            } catch (\Exception $e) {
                Log::error('Failed to send email: ' . $e->getMessage());
                $emailStatus = 'failed';
            }

            return response()->json([
                'success' => true,
                'message' => 'QR code stored and email sent successfully',
                'visitor' => [
                    'id' => $visitor->id,
                    'name' => $visitor->visitor_name,
                    'email' => $visitor->email,
                ],
                'qr_code' => [
                    'url' => Storage::disk('public')->url($fileName),
                    'download_url' => route('visitor.qrcode.download', $visitor->id),
                ],
                'email_status' => $emailStatus,
            ]);

        } catch (\Exception $e) {
            Log::error('QR code storage error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process QR code',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Download QR code
     */
    public function downloadQrCode($id)
    {
        $visitor = Visitor::findOrFail($id);
        
        if (!$visitor->qr_code_path) {
            return response()->json(['error' => 'QR code not found'], 404);
        }

        $path = storage_path('app/public/' . $visitor->qr_code_path);
        
        if (!file_exists($path)) {
            return response()->json(['error' => 'QR code file not found'], 404);
        }

        return response()->download($path, 'visitor-qr-code-' . $visitor->id . '.png');
    }

    /**
 * Generate and download ID card PDF
 */
public function downloadIdCard($id)
{
    try {
        $visitor = Visitor::findOrFail($id);
        
        // Check if QR code exists
        if (!$visitor->qr_code_url) {
            return response()->json(['error' => 'QR code not found. Please generate QR code first.'], 404);
        }

        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.visitor-id-card', [
            'visitor' => $visitor,
            'qrCodeUrl' => $visitor->qr_code_url,
            'appName' => config('app.name', 'Visitor System'),
            'currentDate' => now()->format('F d, Y'),
        ]);

        // Set paper size to ID card size (3.375 × 2.125 inches - standard business card)
        $pdf->setPaper([0, 0, 243, 153], 'portrait'); // 243x153 points = 3.375x2.125 inches
        
        // Download PDF
        return $pdf->download('visitor-id-card-' . $visitor->id . '.pdf');

    } catch (\Exception $e) {
        Log::error('ID Card PDF generation error: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Failed to generate ID card PDF. Please try again.'
        ], 500);
    }
}

/**
 * View ID card PDF
 */
public function viewIdCard($id)
{
    try {
        $visitor = Visitor::findOrFail($id);
        
        // Check if QR code exists
        if (!$visitor->qr_code_url) {
            return response()->json(['error' => 'QR code not found.'], 404);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.visitor-id-card', [
            'visitor' => $visitor,
            'qrCodeUrl' => $visitor->qr_code_url,
            'appName' => config('app.name', 'Visitor System'),
            'currentDate' => now()->format('F d, Y'),
        ]);

        $pdf->setPaper([0, 0, 243, 153], 'portrait');
        
        // View PDF in browser
        return $pdf->stream('visitor-id-card-' . $visitor->id . '.pdf');

    } catch (\Exception $e) {
        Log::error('ID Card PDF view error: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Failed to load ID card. Please try again.'
        ], 500);
    }
}

    /**
     * Display visitor details (API)
     */
    public function show($id)
    {
        try {
            $visitor = Visitor::findOrFail($id);
            
            // Generate frontend URL for the response
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            $cardUrl = $frontendUrl . '/visitor/' . $visitor->id . '/card';
            
            $visitorData = $visitor->toArray();
            $visitorData['card_url'] = $cardUrl;
            
            return response()->json([
                'success' => true,
                'data' => $visitorData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Visitor not found',
            ], 404);
        }
    }
}