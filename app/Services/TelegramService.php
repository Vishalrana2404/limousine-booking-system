<?php

namespace App\Services;

use App\CustomHelper;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class TelegramService
{
    protected $apiToken;
    protected $telegram;

    public function __construct(
        private CustomHelper $helper
    ) {
        $this->apiToken = config('app.telegram_bot_token');
        $this->telegram = new Api($this->apiToken);
    }

    public function sendMessage($chatIds, $message = null, $file = null, $caption = null)
    {
        try {
            $results = [];
            $chatIds = explode(',', $chatIds);

            foreach ($chatIds as $chatId) {
                if ($message) {
                    $response = $this->telegram->sendMessage([
                        'chat_id' => trim($chatId),
                        'text' => $message,
                        'parse_mode' => 'HTML'  
                    ]);

                    $results[] = $response;
                }
                if ($file) {
                         // Check if $file is a local path or URL
                if (filter_var($file, FILTER_VALIDATE_URL)) {
                    // If it's a URL, create InputFile from URL
                    $document = InputFile::create($file);
                } else {
                    // Otherwise, assume it's a local file path
                    $document = InputFile::create($file, basename($file));
                }

                $response = $this->telegram->sendDocument([
                    'chat_id' => trim($chatId),
                    'document' => $document,
                    'caption' => $caption ?? null,
                ]);

                $results[] = $response;
                }
            }

            return $results;
        } catch (\Exception $e) {
            $this->helper->handleException($e);
        }
    }
}
