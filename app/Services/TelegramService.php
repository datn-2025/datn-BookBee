<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/25/18
 * Time: 4:57 PM
 */

namespace App\Services;

use GuzzleHttp\Client;

class TelegramService
{
    private static $_url = 'https://api.telegram.org/bot';
    private static $_token = '7892487295:AAEstS3Lt09uAS6gqJCNu_Du1pTfmEiOghU';
    private static $_chat_id = '-4950673034';

    public function __construct()
    {

    }

    public static function sendMessage($text)
    {
        $uri = self::$_url . self::$_token . '/sendMessage?parse_mode=html';
        $params = [
            'chat_id' => self::$_chat_id,
            'text' => $text,
        ];
        $option['verify'] = false;
        $option['form_params'] = $params;
        $option['http_errors'] = false;
        $client = new Client();
        $response = $client->request("POST", $uri, $option);
        return json_decode($response->getBody(), true);
    }

}
