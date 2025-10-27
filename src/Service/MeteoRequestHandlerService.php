<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MeteoRequestHandlerService
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(env: 'OW_KEY')]
        private readonly string     $apiKey,
        #[Autowire(env: 'OW_COORDINATES_URL')]
        private readonly string     $coordinatesUrl,
        #[Autowire(env: 'OW_WEATHER_URL')]
        private readonly string     $weatherUrl,
    ){}

    public function getCoordinatesFromZipCode(string $zipCode): array
    {
        $response = $this->client->request(
            'GET',
            $this->coordinatesUrl,
            [
                'query' => [
                    'zip' => $zipCode.",FR",
                    'appid' => $this->apiKey,
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
        $meteo = $this->client->request(
            'GET',
            $this->weatherUrl,
            [
                'query' => [
                    'lat' => $coordinates['lat'],
                    'lon' => $coordinates['lon'],
                    'appid' => $this->apiKey,
                ]
            ]
        );

        return [
            'content' => $meteo->getContent(),
            'status' => $meteo->getStatusCode(),
        ];
    }
}
