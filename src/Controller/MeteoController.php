<?php

namespace App\Controller;

use App\Service\MeteoRequestHandlerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


#[Route('/api/meteo', name: 'app_meteo')]
final class MeteoController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{zipCode}', name: 'app_meteo', methods: ['GET'])]
    #[Route('', name: 'app_meteo_current', methods: ['GET'])]
    public function getLocalMeteo(?string $zipCode, TagAwareCacheInterface $cache, MeteoRequestHandlerService $meteoHandler): JsonResponse
    {
        if (!$zipCode) {
            $zipCode = $this->getUser()->getZipCode();
        }

        $cacheId = 'meteo_' . $zipCode;

        $meteo = $cache->get($cacheId, function (ItemInterface $item) use ($zipCode, $meteoHandler) {
            $item->tag('meteo');
            $coordinates = $meteoHandler->getCoordinatesFromZipCode($zipCode);
            return $meteoHandler->getMeteoFromCoordinates($coordinates);
        });

        return new JsonResponse($meteo['content'], $meteo['status'], [], true);
    }
}
