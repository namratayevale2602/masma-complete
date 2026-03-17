<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\VisitorController as WebVisitorController;

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