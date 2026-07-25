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
     * IMPORTANT: credentials are read via config('services.brevo.*'),
     * NOT env() directly. Once `php artisan config:cache` runs (which
     * happens on every Railway deploy), calling env() outside of a
     * config/*.php file silently returns null — even if the variable
     * is genuinely set in the environment. Routing through
     * config/services.php avoids that trap.
     *
     * @return bool true on success, false on failure (never throws)
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlContent): bool
    {
        $apiKey = config('services.brevo.api_key');

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
                    'name'  => config('services.brevo.sender_name'),
                    'email' => config('services.brevo.sender_email'),
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