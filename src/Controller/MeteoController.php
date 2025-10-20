<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


#[Route('/api/meteo', name: 'app_meteo')]
final class MeteoController extends AbstractController
{
    const API_KEY = 'f0a6eff49a91e16ab8039fdd8e38a9d5';

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{zipCode}', name: 'app_meteo', methods: ['GET'])]
    #[Route('', name: 'app_meteo', methods: ['GET'])]
    public function getLocalMeteo(?string $zipCode, HttpClientInterface $client): JsonResponse
    {
        if (!$zipCode) {
            $zipCode = $this->getUser()->getZipCode();
        }

        $coordinates = $this->getCoordinatesFromZipCode($zipCode, $client);

        $url = 'https://api.openweathermap.org/data/2.5/weather';

        $response = $client->request(
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

        return new JsonResponse($response->getContent(), $response->getStatusCode(), [], true);
    }

    private function getCoordinatesFromZipCode(string $zipCode, HttpClientInterface $client): array
    {
        $url = 'http://api.openweathermap.org/geo/1.0/zip';

        $response = $client->request(
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
}
