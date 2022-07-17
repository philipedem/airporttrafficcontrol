<?php

namespace App\Controller;

use App\Entity\Weather;
use App\Repository\AircraftsRepository;
use App\Repository\WeatherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class WeatherController extends AbstractController
{
    /**
     * @Route("/public/weather/forecasts", name="weather")
     */
    public function index(WeatherRepository $repository): JsonResponse
    {
        $data = [];
        //
        $weathers = $repository->findAll();

        if (!$weathers){
            return $this->json(["No Weather Forecasts available"], 404);
        }

        foreach ($weathers as $weather){
            $data[] = [
                "id"            => $$weather->getId(),
                "description"   => $$weather->getDescription(),
                "temperature"   => $$weather->getTemperature(),
                "visibility"    => $$weather->getVisibility(),
                "wind"          => $$weather->getWind(),
                "last_update"   => $weather->getLastUpdate()
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/public/{call_sign}/weather", name="weather_show")
     */
    public function show(AircraftsRepository $repository, WeatherRepository $weatherRepo, string $call_sign): JsonResponse
    {
        $data = [];
        //
        $aircraft = $repository->findOneBy(['callsign' => $call_sign]);

        if (!$aircraft){
            return $this->json(["Invalid Aircraft Call Sign: ". $call_sign], 404);
        }

        $currentWeather = $weatherRepo->getCurrentWeatherUpdate();
        if (!empty($currentWeather)){
            $data = [
                "description"   => $currentWeather['description'],
                "temperature"   => $currentWeather['temperature'],
                "visibility"    => $currentWeather['visibility'],
                "wind"          => $currentWeather['wind'],
                "last_update"   => $currentWeather['last_update']
            ];
        }

        return $this->json($data);
    }
}
