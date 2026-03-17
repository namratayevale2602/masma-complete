<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Display visitor ID card page
     */
    public function showCard($id)
    {
        $visitor = Visitor::find($id);
        
        if (!$visitor) {
            return view('visitor.not-found', [
                'message' => 'Visitor not found or QR code is invalid.'
            ]);
        }

        return view('visitor.card', [
            'visitor' => $visitor,
            'qrCodeUrl' => $visitor->qr_code_url,
        ]);
    }
}