<?php

namespace App\Services;

use Google\Client;
use Google_Service_Drive;

class Telegram
{
    protected $payload;
    protected $config;

    protected function client()
    {
        $guzzClient = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()
        ]);

        return $guzzClient;
    }

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function setRequest($request)
    {
        $update = $request->getContent();
        $this->payload = json_decode($update, true);

        if ($this->payload == null) {
            throw new \Exception('Invalid request content: ' . $update);
        }

        return $this->payload;
    }

    public function sendMessage($chatId, $text, $replyToMessageId = null)
    {
        return $this->client()->post(config('telegram.api_url') . '/sendMessage', [
            \GuzzleHttp\RequestOptions::JSON => [
                'chat_id' => $chatId,
                'text' => $text,
                'reply_to_message_id' => $replyToMessageId,
                'format' => 'markdown'
            ],
        ]);
    }

    public function sendPhoto($chatId, $photo, $replyToMessageId = null)
    {
        return $this->client()->post(config('telegram.api_url') . '/sendPhoto', [
            \GuzzleHttp\RequestOptions::JSON => [
                'chat_id' => $chatId,
                'photo' => $photo,
                'reply_to_message_id' => $replyToMessageId,
                'format' => 'markdown'
            ],
        ]);
    }

    public function setWebhook($url = null)
    {
        return $this->client()->post(config('telegram.api_url') . '/setWebhook', [
            \GuzzleHttp\RequestOptions::JSON => [
                'url' => $url == null ? $this->config['webhook_url'] : $url,
            ],
        ]);
    }
}