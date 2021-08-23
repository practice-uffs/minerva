<?php

namespace App\Http\Controllers;

use App\Services\AuraNLP;
use App\Services\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        Log::info($request->getContent());

        $payload = $this->telegram->setRequest($request);
        
        try {
            $text = $payload['message']['text'];

            $this->saveInteraction($payload);

            $response = null;

            if (stripos($text, '/json') !== false) {
                $response = $this->debugMessage($payload);

            } else {
                $response = $this->processAuraText($payload);
            }

            return $response;

        } catch (\Exception $e) {
            $chatId = @$payload['message']['chat']['id'];

            if (empty($chatId)) {
                return;
            }

            return $this->telegram->sendMessage($chatId, '☠️ Deu ruim: `' . $e->getMessage(). '`');
        }
    }

    protected function saveInteraction($payload)
    {
        try {
            $message = $payload['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'];
            $username = @$message['from']['username'];
            $senderId = @$message['from']['id'];
        } catch (\Exception $e) {
            
        } 
    }

    protected function debugMessage($payload)
    {
        $chatId = $payload['message']['chat']['id'];
        return $this->telegram->sendMessage($chatId, print_r($payload, true));
    }

    protected function processAuraText($payload)
    {
        $text = $payload['message']['text'];

        if (stripos($text, '/domain') !== false) {
            return $this->processAuraTextDomain($payload);

        } else {
            return $this->processAuraTextQna($payload);
        }
    }

    protected function processAuraTextQna($payload)
    {
        $message = $payload['message'];
        $chatId = $message['chat']['id'];
        $text = $message['text'];
        
        $response = $this->auraNLP->qna($text);

        $reply = 'Não entendi, mas você pode me informar outra coisa.';

        if (!empty($response['answer'])) {
            $reply = $response['answer'];
        }

        $debug = stripos($text, '/debug') !== false;

        if ($debug) {
            $reply .= "\n `" . print_r(array_merge($response['entities'], $response['classifications']), true) . '`';
        }

        return $this->telegram->sendMessage($chatId, $reply);
    }    

    protected function processAuraTextDomain($payload)
    {
        $message = $payload['message'];
        $chatId = $message['chat']['id'];
        $text = $message['text'];
        
        $response = $this->auraNLP->domain($text);
        
        $intent = $response['intent'];
        $entities = $response['entities'];
        $sentiment = $response['sentiment']['vote'];

        $reply = [
            'intent' => $intent,
            'entities' => $entities,
            'sentiment' => $sentiment,
        ];

        $debug = stripos($text, '/debug') !== false;

        if ($debug) {
            $reply .= "\n `" . print_r(array_merge($response['entities'], $response['classifications']), true) . '`';
        }

        return $this->telegram->sendMessage($chatId, '`' . print_r($reply, true) . '`');
    }       

    public function setWebhook()
    {
        $url = config('telegram.webhook_url');
        return $this->telegram->setWebhook($url);
    }
}