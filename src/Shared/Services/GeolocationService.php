<?php

declare(strict_types=1);

namespace  BasePackage\Shared\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class GeolocationService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://ipinfo.io/']);
    }

    public function getLatLng()
    {
        $ip = request()->header('CF-Connecting-IP', request()->ip());
        $token = config('services.ipinfo.token'); // Fetch the token from config

        try {
            $cacheKey = 'Location_'.$ip;
            $cachedResponse = Cache::get($cacheKey);

            if ($cachedResponse) {
                return $cachedResponse;
            }


            $response = $this->client->get($ip, [
                'query' => ['token' => $token],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['loc'])) {
                return Cache::rememberForever($cacheKey,function () use ($data) {
                    [$lat, $lng] = explode(',', $data['loc']);
                    return [
                        'lat' => (float) $lat,
                        'lng' => (float) $lng,
                    ];
                });

            }

            return null;
        } catch (\Exception $e) {
            // Handle exceptions or log errors
            return null;
        }
    }
}
