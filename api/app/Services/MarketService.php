<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MarketService
{
    public function getPrices()
    {
        $url = 'https://market.csgo.com/api/v2/prices/RUB.json';

        $response = Http::get($url);

        if(!$response->successful()) {
            return ['success' => false, 'message' => 'Цены не были найдены'];
        }

        $data = $response->json();

        return $data['items'];
    }
}
