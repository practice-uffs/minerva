<?php

namespace App\Http\Controllers;

use App\Services\AuraNLP;
use App\Services\Telegram;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    protected Telegram $telegram;
    protected AuraNLP $auraNLP;

    public function __construct(Telegram $telegram, AuraNLP $auraNLP)
    {
        $this->telegram = $telegram;
        $this->auraNLP = $auraNLP;
    }

    public function index(Request $request)
    {
        //$payload = $this->telegram->setRequest($request);

        $this->processAuraText(null);
        
        try {
            $text = $payload['message']['text'];
            $chatId = $payload['message']['chat']['id'];

            $response = null;

            if (stripos($text, '/json') !== false) {
                $response = $this->debugMessage($payload);

            } else {
                $response = $this->processAuraText($payload);
            }

            return $response;

        } catch (\Exception $e) {
            return $this->telegram->sendMessage($chatId, $e->getMessage());
        }
    }

    protected function debugMessage($payload)
    {
        $chatId = $payload['message']['chat']['id'];
        return $this->telegram->sendMessage($chatId, print_r($payload, true));
    }

    protected function processAuraText($payload)
    {
        $response = $this->auraNLP->qna('oi');
        dd($response);

        $message = $payload['message'];

        $chatId = $message['chat']['id'];
        $text = $message['text'];
        $username = $message['from']['username'];
        $senderId = $message['from']['id'];        
        
        $response = $this->auraNLP->qna($text);

        return $this->telegram->sendMessage($chatId, print_r($response));
    }

    public function setWebhook()
    {
        $url = config('telegram.webhook_url');
        return $this->telegram->setWebhook($url);
    }
}