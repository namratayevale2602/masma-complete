<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Web\VisitorController as WebVisitorController;
use App\Models\Registration;
use App\Services\CertificateService;

Route::get('/test-certificate/{id}', function($id) {
    $registration = \App\Models\Registration::find($id);
    if (!$registration) {
        return response()->json(['error' => 'Registration not found'], 404);
    }
    
    try {
        $service = new \App\Services\CertificateService();
        $certificatePath = $service->generateCertificate($registration);
        
        if (file_exists($certificatePath)) {
            // Return the image with proper headers to display in browser
            return response()->file($certificatePath, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename="certificate.png"'
            ]);
        } else {
            return response()->json(['error' => 'Certificate file not found'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Test payment receipt endpoint
Route::get('/test-receipt/{id}', function($id) {
    $registration = \App\Models\Registration::find($id);
    if (!$registration) {
        return response()->json(['error' => 'Registration not found'], 404);
    }
    
    try {
        $service = new \App\Services\CertificateService();
        $receiptPath = $service->generatePaymentReceipt($registration);
        
        if (file_exists($receiptPath)) {
            return response()->file($receiptPath, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename="payment-receipt.png"'
            ]);
        } else {
            return response()->json(['error' => 'Receipt file not found'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/registration/{registration}/view-form', function (Registration $registration) {
    return view('downloads.registration', ['registration' => $registration]);
})->name('registration.view-form');

// QR Code Display Page
Route::get('/visitor/{id}/card', [WebVisitorController::class, 'showCard'])
    ->name('visitor.card');
    
    


// Test QR Code Page
Route::get('/test-qr/{id}', function ($id) {
    $visitor = \App\Models\Visitor::find($id);
    
    if (!$visitor) {
        return "Visitor not found";
    }
    
    $url = url('/visitor/' . $id . '/card');
    
    return view('test-qr', [
        'visitor' => $visitor,
        'url' => $url,
        'qrCode' => \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($url),
    ]);
});

// Test route for PDF view
Route::get('/test-id-card/{id}', function($id) {
    $visitor = \App\Models\Visitor::find($id);
    
    if (!$visitor) {
        return 'Visitor not found';
    }
    
    // Get QR code as base64
    $qrCodeImageData = Storage::disk('public')->get($visitor->qr_code_path);
    $qrCodeBase64 = base64_encode($qrCodeImageData);
    
    // Render the Blade view in browser
    return view('pdf.visitor-id-card', [
        'visitor' => $visitor,
        'qrCodeImage' => $qrCodeBase64,
        'appName' => config('app.name'),
    ]);
});

// Test route for email view
Route::get('/test-email/{id}', function($id) {
    $visitor = \App\Models\Visitor::find($id);
    
    if (!$visitor) {
        return 'Visitor not found';
    }
    
    return view('emails.visitor-qrcode', [
        'visitor' => $visitor,
        'appName' => config('app.name'),
        'cardUrl' => config('app.frontend_url', 'http://localhost:5173') . '/visitor/' . $visitor->id . '/card',
    ]);
});

Route::get('/test-pdf-print/{id}', function($id) {
    $visitor = \App\Models\Visitor::find($id);
    
    if (!$visitor) {
        return 'Visitor not found';
    }
    
    // Check if QR code exists
    if (!$visitor->qr_code_path || !Storage::disk('public')->exists($visitor->qr_code_path)) {
        return 'QR code not found for visitor ID: ' . $id;
    }
    
    // Get QR code as base64
    $qrCodeImageData = Storage::disk('public')->get($visitor->qr_code_path);
    $qrCodeBase64 = base64_encode($qrCodeImageData);
    
    // Generate PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.visitor-id-card', [
        'visitor' => $visitor,
        'qrCodeImage' => $qrCodeBase64,
        'appName' => config('app.name'),
    ]);
    
    // Set ID card size (85mm x 54mm = 3.35" x 2.13")
    // Convert mm to points: 1mm = 2.83465 points
    $width = 85 * 2.83465;  // 85mm in points
    $height = 54 * 2.83465; // 54mm in points
    
    $pdf->setPaper([0, 0, $width, $height], 'portrait');
    
    // Set PDF options for printing
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => false,
        'defaultFont' => 'helvetica',
        'dpi' => 300,
        'isPhpEnabled' => true,
    ]);
    
    // View in browser (stream) instead of downloading
    return $pdf->stream('visitor-id-card-' . $visitor->id . '.pdf');
});



Route::get('/', function () {
    return view('welcome');
});


// Payment Screenshot Route
Route::get('/file/screenshot/{filename}', function ($filename) {
    $path = 'payment-screenshots/' . $filename;
    
    // Log for debugging
    \Log::info('Serving screenshot', [
        'filename' => $filename,
        'path' => $path,
        'full_path' => storage_path('app/public/' . $path),
        'exists' => Storage::disk('public')->exists($path)
    ]);
    
    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'File not found: ' . $filename);
    }
    
    $file = Storage::disk('public')->get($path);
    $mimeType = Storage::disk('public')->mimeType($path);
    
    return Response::make($file, 200)
        ->header('Content-Type', $mimeType)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
})->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$')->name('payment.screenshot');

// Applicant Photo Route
Route::get('/file/applicant/{filename}', function ($filename) {
    $path = 'applicant-photos/' . $filename;
    
    \Log::info('Serving applicant photo', [
        'filename' => $filename,
        'path' => $path,
        'full_path' => storage_path('app/public/' . $path),
        'exists' => Storage::disk('public')->exists($path)
    ]);
    
    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'File not found: ' . $filename);
    }
    
    $file = Storage::disk('public')->get($path);
    $mimeType = Storage::disk('public')->mimeType($path);
    
    return Response::make($file, 200)
        ->header('Content-Type', $mimeType)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
})->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$')->name('applicant.photo');

// Visiting Card Route
Route::get('/file/visitingcard/{filename}', function ($filename) {
    $path = 'visiting-cards/' . $filename;
    
    \Log::info('Serving visiting card', [
        'filename' => $filename,
        'path' => $path,
        'full_path' => storage_path('app/public/' . $path),
        'exists' => Storage::disk('public')->exists($path)
    ]);
    
    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'File not found: ' . $filename);
    }
    
    $file = Storage::disk('public')->get($path);
    $mimeType = Storage::disk('public')->mimeType($path);
    
    return Response::make($file, 200)
        ->header('Content-Type', $mimeType)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
})->where('filename', '.*\.(jpg|jpeg|png|gif|webp)$')->name('visiting.card');


// Certificate Routes
Route::get('/certificate/view/{id}', function($id) {
    $registration = \App\Models\Registration::find($id);
    if (!$registration) {
        abort(404, 'Registration not found');
    }
    
    if (!$registration->payment_verified) {
        abort(403, 'Payment not verified yet');
    }
    
    try {
        $service = new \App\Services\CertificateService();
        
        // Check if certificate exists, if not generate it
        $memberId = $registration->member_id ?? $registration->parent_member_id;
        $certificatePath = storage_path("app/public/certificates/certificate_{$registration->id}_{$memberId}.png");
        
        if (!file_exists($certificatePath)) {
            $certificatePath = $service->generateCertificate($registration);
        }
        
        if (file_exists($certificatePath)) {
            return response()->file($certificatePath, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename="certificate.png"'
            ]);
        } else {
            abort(404, 'Certificate not found');
        }
    } catch (\Exception $e) {
        abort(500, $e->getMessage());
    }
})->name('certificate.view');

Route::get('/certificate/download/{id}', function($id) {
    $registration = \App\Models\Registration::find($id);
    if (!$registration) {
        abort(404, 'Registration not found');
    }
    
    if (!$registration->payment_verified) {
        abort(403, 'Payment not verified yet');
    }
    
    try {
        $service = new \App\Services\CertificateService();
        
        $memberId = $registration->member_id ?? $registration->parent_member_id;
        $certificatePath = storage_path("app/public/certificates/certificate_{$registration->id}_{$memberId}.png");
        
        if (!file_exists($certificatePath)) {
            $certificatePath = $service->generateCertificate($registration);
        }
        
        if (file_exists($certificatePath)) {
            return response()->download($certificatePath, "membership-certificate-{$memberId}.png", [
                'Content-Type' => 'image/png',
            ]);
        } else {
            abort(404, 'Certificate not found');
        }
    } catch (\Exception $e) {
        abort(500, $e->getMessage());
    }
})->name('certificate.download');

// Receipt Routes
Route::get('/receipt/view/{id}', function($id) {
    $registration = \App\Models\Registration::find($id);
    if (!$registration) {
        abort(404, 'Registration not found');
    }
    
    if (!$registration->payment_verified) {
        abort(403, 'Payment not verified yet');
    }
    
    try {
        $service = new \App\Services\CertificateService();
        
        $memberId = $registration->member_id ?? $registration->parent_member_id;
        $receiptPath = storage_path("app/public/receipts/receipt_{$registration->id}_{$memberId}.png");
        
        if (!file_exists($receiptPath)) {
            $receiptPath = $service->generatePaymentReceipt($registration);
        }
        
        if (file_exists($receiptPath)) {
            return response()->file($receiptPath, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename="payment-receipt.png"'
            ]);
        } else {
            abort(404, 'Receipt not found');
        }
    } catch (\Exception $e) {
        abort(500, $e->getMessage());
    }
})->name('receipt.view');

Route::get('/receipt/download/{id}', function($id) {
    $registration = \App\Models\Registration::find($id);
    if (!$registration) {
        abort(404, 'Registration not found');
    }
    
    if (!$registration->payment_verified) {
        abort(403, 'Payment not verified yet');
    }
    
    try {
        $service = new \App\Services\CertificateService();
        
        $memberId = $registration->member_id ?? $registration->parent_member_id;
        $receiptPath = storage_path("app/public/receipts/receipt_{$registration->id}_{$memberId}.png");
        
        if (!file_exists($receiptPath)) {
            $receiptPath = $service->generatePaymentReceipt($registration);
        }
        
        if (file_exists($receiptPath)) {
            return response()->download($receiptPath, "payment-receipt-{$memberId}.png", [
                'Content-Type' => 'image/png',
            ]);
        } else {
            abort(404, 'Receipt not found');
        }
    } catch (\Exception $e) {
        abort(500, $e->getMessage());
    }
})->name('receipt.download');