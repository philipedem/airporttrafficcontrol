<?php

namespace App\Controller;

use App\Entity\Aircrafts;
use App\Repository\AircraftsRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use App\Repository\LocationsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/api", name="api_")
 */
class AircraftsController extends AbstractController
{
    /**
     * @Route("/aircrafts", name="aircrafts")
     */
    public function index(AircraftsRepository $repository): JsonResponse
    {
        $data = [];
        //
        $aircrafts = $repository->findAll();

        if (!$aircrafts){
            return $this->json(["No Aircrafts available"], 404);
        }

        foreach ($aircrafts as $craft){
            $data[] = [
                "id"        => $craft->getId(),
                "name"      => $craft->getAircraftName(),
                "type"      => $craft->getAircraftType(),
                "capacity"  => $craft->getAircraftCapacity(),
                "state"     => $craft->getAircraftCurrentState()
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/aircrafts/{call_sign}", name="aircrafts_show")
     */
    public function show(AircraftsRepository $repository, string $call_sign): JsonResponse
    {
        $data = [];
        //
        $aircraft = $repository->findOneBy(['callsign' => $call_sign]);
        //$aircraft = $doctrine->getRepository(Aircrafts::class)->findOneBy(['callsign' => $call_sign]);

        if (!$aircraft){
            return $this->json(["No Aircraft found for Call Sign: ". $call_sign], 404);
        }

        $data = [
            "id"        => $aircraft->getId(),
            "name"      => $aircraft->getAircraftName(),
            "type"      => $aircraft->getAircraftType(),
            "capacity"  => $aircraft->getAircraftCapacity(),
            "state"     => $aircraft->getAircraftCurrentState()
        ];

        return $this->json($data);
    }

    /**
     * @Route("/aircrafts", name="aircrafts_creat")
     */
    public function new(AircraftsRepository $repository, UserRepository $userRepo, ApiTokenRepository $tokenRepo, UserPasswordHasherInterface $passwordHasher, Request $request, ValidatorInterface $validator): JsonResponse
    {
        //Extract Request input parameters
        $_REQUEST = json_decode($request->getContent(),true);
        //Get request data into new aircraft object
        $aircraft = new Aircrafts();
        $aircraft->setAircraftName($_REQUEST['name']);
        $aircraft->setAircraftType($_REQUEST['type']);
        $aircraft->setAircraftCapacity($_REQUEST['capacity']);
        $aircraft->setAircraftCallSign($_REQUEST['callsign']);

        //$aircraft->setAircraftCurrentState('PARKED');
        // Validate input data
        $errors = $validator->validate($aircraft);
        if (count($errors) > 0){
            return $this->json([(string) $errors], 400);
        }
        //save new record
        $repository->add($aircraft, true);
        //
        //create a user account for newly created aircraft
        $_user = new User();
        $_user->setCallsign($_REQUEST['callsign']);
        $_user->setRoles(array());
        //
        $hashedPassword = $passwordHasher->hashPassword(
            $_user,
            $_REQUEST['callsign']
        );
        //
        $_user->setPassword($hashedPassword);
        $userRepo->add($_user, true);
        //
        $_token = new ApiToken();
        $_token->setToken($hashedPassword);
        $_token->setUser($_user);
        $tokenRepo->add($_token, true);
        
        return $this->json([
            "message" => "Created new Aircraft successfully.",
            "token" => $hashedPassword
        ]);
    }

    /**
     * @Route("/aircrafts/{id}", name="aircrafts_update")
     */
    public function update(ManagerRegistry $doctrine, int $id, Request $request)
    {
        // find aircraft to be updated with new details
        $entityManager = $doctrine->getManager();
        $aircraft = $entityManager->getRepository(Aircrafts::class)->find($id);
        //
        if (!$aircraft){
            return $this->json(['No Aircraft found for id '.$id], 404);
        }
        //
        //Extract Request input parameters
        $_REQUEST = json_decode($request->getContent(),true);
        //set new values
        $aircraft->setAircraftName($_REQUEST['name']);
        $aircraft->setAircraftType($_REQUEST['type']);
        $aircraft->setAircraftCapacity($_REQUEST['capacity']);
        //
        $entityManager->flush();
        //
        return $this->redirectToRoute('api_aircrafts_show', [
            'call_sign' => $aircraft->getAircraftCallSign()
        ]);
    }

    /**
     * @Route("/aircrafts/{call_sign}/locations", name="aircrafts_locations")
     */
    public function locations(AircraftsRepository $repository, LocationsRepository $locRepository, string $call_sign): JsonResponse
    {
        $data = [];
        //
        $aircraft = $repository->findOneBy(['callsign' => $call_sign]);

        if (!$aircraft){
            return $this->json(["No Aircraft found for Call Sign: ". $call_sign], 404);
        }
        //
        //Get list of locations for found aircraft
        $aircraftLocations = [];
        $locations = $locRepository->findBy(['aircraft' => $aircraft->getId()], ['created' => 'DESC']);
        foreach ($locations as $loc){
            $aircraftLocations[] = [
                "latitude"  => $loc->getAircraftLatitude(),
                "longitude" => $loc->getAircraftLongitude(),
                "altitude"  => $loc->getAircraftAltitude(),
                "heading"   => $loc->getAircraftHeading(),
                "created"   => $loc->getCreatedAt(),
            ];
        }
        //get the locations for selected aircraft
        $data = [
            "id"        => $aircraft->getId(),
            "name"      => $aircraft->getAircraftName(),
            "type"      => $aircraft->getAircraftType(),
            "capacity"  => $aircraft->getAircraftCapacity(),
            "state"     => $aircraft->getAircraftCurrentState(),
            "locations" => $aircraftLocations
        ];

        return $this->json($data);
    }
}
