<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MeteoRequestHandlerService
{
    const API_KEY = 'f0a6eff49a91e16ab8039fdd8e38a9d5';
    public function __construct(
        private HttpClientInterface $client,
    ){}

    public function getCoordinatesFromZipCode(string $zipCode): array
    {
        $url = 'http://api.openweathermap.org/geo/1.0/zip';

        $response = $this->client->request(
            'GET',
            $url,
            [
                'query' => [
                    'zip' => $zipCode.",FR",
                    'appid' => self::API_KEY,
                ],
            ]
        );

        $data = $response->toArray();
        return [
            'lat' => $data['lat'],
            'lon' => $data['lon'],
        ];
    }

    public function getMeteoFromCoordinates(array $coordinates): array
    {
        $url = 'https://api.openweathermap.org/data/2.5/weather';

        $meteo = $this->client->request(
            'GET',
            $url,
            [
                'query' => [
                    'lat' => $coordinates['lat'],
                    'lon' => $coordinates['lon'],
                    'appid' => self::API_KEY,
                ]
            ]
        );

        return [
            'content' => $meteo->getContent(),
            'status' => $meteo->getStatusCode(),
        ];
    }
}
