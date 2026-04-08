<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Send a message to Telegram chat
     *
     * @param Company $company
     * @param string $message
     * @param array $options
     * @return bool
     */
    public function sendMessage(Company $company, string $message, array $options = []): bool
    {
        // Check if Telegram is enabled for this company
        if (!$company->telegram_enabled || !$company->telegram_bot_token || !$company->telegram_chat_id) {
            Log::info('Telegram notification skipped', [
                'company_id' => $company->id,
                'reason' => 'Telegram not configured or disabled',
                'enabled' => $company->telegram_enabled ?? false,
                'has_token' => !empty($company->telegram_bot_token),
                'has_chat_id' => !empty($company->telegram_chat_id),
            ]);
            return false;
        }

        try {
            $botToken = $company->telegram_bot_token;
            $chatId = $company->telegram_chat_id;
            
            // Telegram Bot API endpoint
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            
            // Prepare message with optional formatting
            $parseMode = $options['parse_mode'] ?? 'HTML';
            $disableWebPagePreview = $options['disable_web_page_preview'] ?? true;
            
            $payload = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => $disableWebPagePreview,
            ];

            // Add optional reply_markup for buttons
            if (isset($options['reply_markup'])) {
                $payload['reply_markup'] = $options['reply_markup'];
            }

            Log::info('Sending Telegram message', [
                'company_id' => $company->id,
                'chat_id' => $chatId,
                'message_length' => strlen($message),
            ]);

            // Use withoutVerifying() for development environments with SSL issues
            // For production, ensure proper SSL certificates are configured
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->post($url, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                if ($responseData['ok'] ?? false) {
                    Log::info('Telegram message sent successfully', [
                        'company_id' => $company->id,
                        'message_id' => $responseData['result']['message_id'] ?? null,
                    ]);
                    return true;
                }
            }

            Log::error('Telegram API error', [
                'company_id' => $company->id,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Telegram service exception', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Test Telegram connection
     *
     * @param string $botToken
     * @param string $chatId
     * @return array
     */
    public function testConnection(string $botToken, string $chatId): array
    {
        try {
            $url = "https://api.telegram.org/bot{$botToken}/getMe";
            
            // Use withoutVerifying() for development environments with SSL issues
            // For production, ensure proper SSL certificates are configured
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($url);
            
            if (!$response->successful() || !($response->json()['ok'] ?? false)) {
                return [
                    'success' => false,
                    'message' => 'Invalid bot token. Please check your bot token.',
                ];
            }

            // Test sending a message
            $testMessage = "✅ Test message from Phishing Simulation Platform\n\n" . 
                          "Time: " . now()->format('Y-m-d H:i:s') . "\n" .
                          "This is a test notification to verify your Telegram configuration.";

            $sendUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $sendResponse = Http::timeout(10)
                ->withoutVerifying()
                ->post($sendUrl, [
                    'chat_id' => $chatId,
                    'text' => $testMessage,
                    'parse_mode' => 'HTML',
                ]);

            if ($sendResponse->successful() && ($sendResponse->json()['ok'] ?? false)) {
                return [
                    'success' => true,
                    'message' => 'Telegram connection successful! Test message sent.',
                    'bot_info' => $response->json()['result'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => 'Bot token is valid but failed to send message. Please check your chat ID.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Format campaign notification message
     *
     * @param string $type
     * @param array $data
     * @return string
     */
    public function formatCampaignMessage(string $type, array $data): string
    {
        $messages = [
            'launched' => "🚀 <b>Campaign Launched</b>\n\n" .
                         "Campaign: <b>{$data['campaign_name']}</b>\n" .
                         "Type: {$data['campaign_type']}\n" .
                         "Targets: {$data['targets_count']} employees\n" .
                         "Time: {$data['time']}",
            
            'completed' => "✅ <b>Campaign Completed</b>\n\n" .
                          "Campaign: <b>{$data['campaign_name']}</b>\n" .
                          "Type: {$data['campaign_type']}\n" .
                          "Results:\n" .
                          "• Opened: {$data['opened_count']}\n" .
                          "• Clicked: {$data['clicked_count']}\n" .
                          "• Submitted: {$data['submitted_count']}\n" .
                          "Time: {$data['time']}",
            
            'high_risk' => "⚠️ <b>High-Risk Alert</b>\n\n" .
                          "An employee submitted credentials!\n\n" .
                          "Campaign: <b>{$data['campaign_name']}</b>\n" .
                          "Employee: {$data['employee_name']} ({$data['employee_email']})\n" .
                          "Time: {$data['time']}\n\n" .
                          "Immediate action recommended!",
        ];

        return $messages[$type] ?? "📧 Notification: " . json_encode($data);
    }
}

