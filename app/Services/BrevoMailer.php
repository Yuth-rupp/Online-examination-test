<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailer
{
    /**
     * Send a transactional email through Brevo's HTTPS API.
     * We use the API directly (not SMTP) because Railway's free/hobby
     * plan blocks outbound SMTP connections but allows HTTPS.
     *
     * @return bool true on success, false on failure (never throws)
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlContent): bool
    {
        $apiKey = env('BREVO_API_KEY');

        if (!$apiKey) {
            Log::error('BrevoMailer: BREVO_API_KEY is not set.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'accept'       => 'application/json',
                'api-key'      => $apiKey,
                'content-type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name'  => env('BREVO_SENDER_NAME', env('MAIL_FROM_NAME', 'ExamSystem')),
                    'email' => env('BREVO_SENDER_EMAIL', env('MAIL_FROM_ADDRESS')),
                ],
                'to' => [
                    ['email' => $toEmail, 'name' => $toName],
                ],
                'subject'     => $subject,
                'htmlContent' => $htmlContent,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('BrevoMailer: send failed', [
                'to'     => $toEmail,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('BrevoMailer: exception while sending', [
                'to'    => $toEmail,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}