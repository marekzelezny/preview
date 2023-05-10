<?php

namespace App\Extensions\Ecomail;

use Illuminate\Support\Facades\Http;

class EcomailChannel
{
    public function send($notifiable, $notification): void
    {
        $message = $notification->toEcomail($notifiable);

        $response = Http::withHeaders([
            'key' => config('services.ecomail.apiKey'),
        ])->post(
            url: 'https://api2.ecomailapp.cz/transactional/send-template',
            data: ['message' => $message->structure()]
        )->object();
    }
}
