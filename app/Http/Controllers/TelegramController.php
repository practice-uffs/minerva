<?php

namespace App\Http\Controllers;

use App\Services\Telegram;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    protected Telegram $telegram;

    public function __construct(Telegram $telegram)
    {
        $this->telegram = $telegram;
    }

    public function index(Request $request)
    {
        $payload = $this->telegram->setRequest($request);
        
        $message = $payload['message'];
        $chatId = $message['chat']['id'];
        $text = $message['text'];
        $username = $message['from']['username'];
        $senderId = $message['from']['id'];

        $image = url('qrcode.png');

        $this->telegram->sendMessage($chatId, 'Oi!');
        $this->telegram->sendPhoto($chatId, $image);

    }

    // A method that invokes the setWebhook method of the Telegram API
    public function setWebhook()
    {
        $url = config('telegram.webhook_url');
        return $this->telegram->setWebhook($url);
    }
}