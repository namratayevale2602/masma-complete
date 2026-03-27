<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = env('WHATSAPP_API_URL', 'https://partners.pinbot.ai/v1/messages');
        $this->apiKey = env('WHATSAPP_API_KEY');
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER');
    }

    /**
     * Send WhatsApp template message
     */
    public function sendTemplateMessage($contact, $templateName, $parameters = [])
    {
        if (!$this->apiKey || !$this->phoneNumberId) {
            Log::warning('WhatsApp API credentials not configured');
            return ['success' => false, 'error' => 'API credentials not configured'];
        }

        // Format contact number
        $contact = preg_replace('/[^0-9]/', '', $contact);
        if (strlen($contact) === 10) {
            $contact = '91' . $contact;
        }

        $data = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $contact,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => ["code" => "en"],
                "components" => []
            ]
        ];

        // Add components if parameters exist
        if (!empty($parameters)) {
            $components = [];
            
            // Handle header parameters
            if (isset($parameters['header'])) {
                $components[] = [
                    "type" => "header",
                    "parameters" => array_map(function($param) {
                        return ["type" => "text", "text" => $param];
                    }, $parameters['header'])
                ];
            }
            
            // Handle body parameters
            if (isset($parameters['body'])) {
                $components[] = [
                    "type" => "body",
                    "parameters" => array_map(function($param) {
                        return ["type" => "text", "text" => $param];
                    }, $parameters['body'])
                ];
            }
            
            // Handle button parameters
            if (isset($parameters['buttons'])) {
                foreach ($parameters['buttons'] as $index => $buttonText) {
                    $components[] = [
                        "type" => "button",
                        "sub_type" => "quick_reply",
                        "index" => $index,
                        "parameters" => [
                            ["type" => "text", "text" => $buttonText]
                        ]
                    ];
                }
            }
            
            $data['template']['components'] = $components;
        }

        return $this->sendRequest($data);
    }

    /**
     * Send WhatsApp request
     */
    private function sendRequest($data)
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'apikey: ' . $this->apiKey,
                    'wanumber: ' . $this->phoneNumberId
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            $response = curl_exec($curl);
            $error = curl_error($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($error) {
                Log::error('WhatsApp API Error: ' . $error);
                return ['success' => false, 'error' => $error];
            }

            $decoded = json_decode($response, true);
            
            if ($httpCode >= 400 || isset($decoded['error'])) {
                $errorMsg = $decoded['error']['message'] ?? 'Unknown WhatsApp API error';
                Log::error('WhatsApp API Response Error: ' . $errorMsg);
                return ['success' => false, 'error' => $errorMsg];
            }

            Log::info('WhatsApp template message sent successfully');
            return ['success' => true, 'response' => $decoded];

        } catch (\Exception $e) {
            Log::error('WhatsApp Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}