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
            $data[] = [
                "description"   => "broken clouds",
                "temperature"   => 300.36,
                "visibility"    => 10000,
                "wind"          => [
                    "speed" => 5.97,
                    "deg"   => 213
                ],
                "last_update"   => date('Y-m-d H:i:s')
            ];
            return $this->json($data, 404);
        }

        foreach ($weathers as $weather){
            $data[] = [
                "description"   => $weather->getDescription(),
                "temperature"   => $weather->getTemperature(),
                "visibility"    => $weather->getVisibility(),
                "wind"          => $weather->getWind(),
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
        if ($currentWeather){
            $data = [
                "description"   => $currentWeather->getDescription(),
                "temperature"   => $currentWeather->getTemperature(),
                "visibility"    => $currentWeather->getVisibility(),
                "wind"          => $currentWeather->getWind(),
                "last_update"   => $currentWeather->getLastUpdate()
            ];
        }
        //
        else{
            $data = [
                "description"   => "broken clouds",
                "temperature"   => 300.36,
                "visibility"    => 10000,
                "wind"          => [
                    "speed" => 5.97,
                    "deg"   => 213
                ],
                "last_update"   => date('Y-m-d H:i:s')
            ];
        }

        return $this->json($data);
    }
}
