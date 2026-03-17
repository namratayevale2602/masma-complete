<?php
// app/Http/Controllers/Api/ContactMessageController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Mail\ContactFormSubmitted;
use App\Mail\AdminContactNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'consent' => 'required|boolean|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $message = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'consent_given' => $request->consent,
        ]);

        // Send email to user (optional)
        try {
            // Mail::to($request->email)->send(new ContactFormSubmitted($message));
            // Mail::to('admin@masma.in')->send(new AdminContactNotification($message));
        } catch (\Exception $e) {
            // Log email error but don't fail the request
            \Log::error('Email sending failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your message! We\'ll get back to you soon.',
            'data' => $message,
        ], 201);
    }

    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->markAsRead();
        
        return response()->json([
            'success' => true,
            'data' => $message,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $message = ContactMessage::findOrFail($id);
        
        // Send reply email
        try {
            // Mail::to($message->email)->send(new ContactReply($message, $request->reply));
        } catch (\Exception $e) {
            \Log::error('Reply email failed: ' . $e->getMessage());
        }

        $message->markAsReplied($request->reply);

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
        ]);
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }
}