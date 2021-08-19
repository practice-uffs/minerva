<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected function client() {
        $guzzClient = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()
        ]);

        return $guzzClient;
    }

    public function index(Request $request)
    {
        $update = $request->getContent();
        $input = json_decode($update, true);

        Log::info($input);

        $message = $input['message'];

        $chatId = $message['chat']['id'];
        $text = $message['text'];
        $username = $message['from']['username'];

        $senderId = $message['from']['id'];

        $response = $this->client()->post(config('telegram.api_url') . '/sendMessage', [
            \GuzzleHttp\RequestOptions::JSON => [
                'chat_id' => $chatId,
                'text' => 'Hello ' . $username . '!',
                'reply_to_message_id' => $message['message_id'],
            ],
        ]);

        return '';
    }

    // A method that invokes the setWebhook method of the Telegram API
    public function setWebhook()
    {
        $response = $this->client()->post(config('telegram.api_url') . '/setWebhook', [
            \GuzzleHttp\RequestOptions::JSON => [
                'url' => config('telegram.webhook_url'),
            ],
        ]);

        return $response;
    }
}