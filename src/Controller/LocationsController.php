<?php

namespace App\Controller;

use App\Entity\Aircrafts;
use App\Repository\AircraftsRepository;
use App\Entity\Locations;
use App\Repository\LocationsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api", name="api_")
 */
class LocationsController extends AbstractController
{
    /**
     * @Route("/locations", name="locations")
     */
    public function index(LocationsRepository $repository): JsonResponse
    {
        //get location data
        $locations = $repository->getAllAircraftsLocations();
        //
        return $this->json($locations);
    }

    /**
     * @Route("/{call_sign}/location", name="locations_update")
     */
    public function update(LocationsRepository $locRepository, AircraftsRepository $craftRepository, Request $request, string $call_sign): Response
    {
        $response = new Response();
        //get details of aircraft whose location details to be logged
        $aircraft = $craftRepository->findOneBy(['callsign' => $call_sign]);
        //
        if (!$aircraft){
            //return $this->json(["No Aircraft found for Call Sign: ". $call_sign], 404);
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }
        //
        //Extract Request input parameters
        $_REQUEST = json_decode($request->getContent(),true);
        $_date = new \DateTime();
        //
        //Get request data into new aircraft location object
        $location = new Locations();
        $location->setAircraftId($aircraft);
        $location->setAircraftType($_REQUEST['type']);
        $location->setAircraftLatitude($_REQUEST['latitude']);
        $location->setAircraftLongitude($_REQUEST['longitude']);
        $location->setAircraftAltitude($_REQUEST['altitude']);
        $location->setAircraftHeading($_REQUEST['heading']);
        $location->setCreatedAt($_date);
        //add new collected data
        $locRepository->add($location, true);

        
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        return $response;
    }
}
