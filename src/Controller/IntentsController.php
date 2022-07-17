<?php

namespace App\Controller;

use App\Entity\Intents;
use App\Repository\IntentsRepository;
use App\Repository\AircraftsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api", name="api_")
 */
class IntentsController extends AbstractController
{
    /**
     * @Route("/intents", name="intents")
     */
    public function index(IntentsRepository $repository): JsonResponse
    {
        //get all aircrafts intent data
        $intents = $repository->getAllAircraftsIntents();
        //
        return $this->json($intents);
    }

    /**
     * @Route("/{call_sign}/intent", name="intents_update")
     */
    public function create(IntentsRepository $intRepository, AircraftsRepository $craftRepository, Request $request, string $call_sign): Response
    {
        $response = new Response();
        //get details of aircraft whose location details to be logged
        $aircraft = $craftRepository->findOneBy(['callsign' => $call_sign]);
        //
        if (!$aircraft)
        {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }
        //
        //Extract Request input parameters
        $_REQUEST = json_decode($request->getContent(),true);
        //
        //Get request data into new aircraft intent object
        $intent = new Intents();
        $intent->setAircraftId($aircraft);
        $intent->setState($_REQUEST['state']);
        $intent->setStatus("NEW");
        $intent->setCreated(new \DateTime());
        //add new collected data
        $intRepository->add($intent, true);
        //
        //aircraft request to TAKE-OFF
        if ($_REQUEST['state'] === 'TAKE-OFF')
        {
            //aircraft current state === PARKED
            if ($aircraft->getAircraftCurrentState() === 'PARKED')
            {
                //Check if there is no aircraft on the RUN-WAY
                if (!$craftRepository->isRunWayOccupied()){
                    //RUN-WAY is free, approve TAKE-OFF for aircraft
                    $savedIntent = $intRepository->processIntentRequestApproval($intent->getId(), "ACCEPTED");
                    if ($savedIntent){
                        //update aircraft state to TAKE-OFF
                        $changedState = $craftRepository->changeAircraftState($aircraft->getId(), $_REQUEST['state']);
                        //
                        if ($changedState)
                            $response->setStatusCode(Response::HTTP_NO_CONTENT);
                        else
                            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                    }
                    else
                        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                }
                else{
                    //RUN-WAY is NOT free, disapprove TAKE-OFF for aircraft
                    $savedIntent = $intRepository->processIntentRequestApproval($intent->getId(), "REJECTED");
                    //
                    $response->setStatusCode(Response::HTTP_CONFLICT);
                }
            }
            else
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        //aircraft notify it is AIRBORNE
        elseif ($_REQUEST['state'] === 'AIRBORNE')
        {
            //aircraft current state === TAKE-OFF OR APPROACH
            if (in_array($aircraft->getAircraftCurrentState(), array('TAKE-OFF','APPROACH')))
            {
                //approve AIRBORNE notification intent for aircraft
                $savedIntent = $intRepository->processIntentRequestApproval($intent->getId(), "ACCEPTED");
                if ($savedIntent){
                    //update aircraft state to AIRBORNE
                    $changedState = $craftRepository->changeAircraftState($aircraft->getId(), $_REQUEST['state']);
                    //
                    if ($changedState)
                        $response->setStatusCode(Response::HTTP_NO_CONTENT);
                    else
                        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                }
                else
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
            else{
                //An Aircraft already on approach to land, disapprove APPROACH for aircraft
                $savedIntent = $intRepository->processIntentRequestApproval($intent->getId(), "REJECTED");
                //
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
        }
        //aircraft notify to be on the APPROACH
        elseif ($_REQUEST['state'] === 'APPROACH')
        {
            //aircraft current state === AIRBORNE
            if ($aircraft->getAircraftCurrentState() === 'AIRBORNE')
            {
                //Check if there is NO aircraft on APPROACH
                if (!$craftRepository->isAnAircraftOnApproachOrLanded()){
                    //RUN-WAY is free, approve APPROACH for aircraft
                    $savedIntent = $intRepository->processIntentRequestApproval($intent->getId(), "ACCEPTED");
                    if ($savedIntent){
                        //update aircraft state to APPROACH
                        $changedState = $craftRepository->changeAircraftState($aircraft->getId(), $_REQUEST['state']);
                        //
                        if ($changedState)
                            $response->setStatusCode(Response::HTTP_NO_CONTENT);
                        else
                            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                    }
                    else
                        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                }
                else{
                    //An Aircraft already on approach to land, disapprove APPROACH for aircraft
                    $savedIntent = $intRepository->processIntentRequestApproval($intent->getId(), "REJECTED");
                    //
                    $response->setStatusCode(Response::HTTP_CONFLICT);
                }
            }
            else{
                //An Aircraft already on approach to land, disapprove APPROACH for aircraft
                $savedIntent = $intRepository->processIntentRequestApproval($intent->getId(), "REJECTED");
                //
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
        }
        //aircraft notifiy to have LANDED successfully
        elseif ($_REQUEST['state'] === 'LANDED')
        {
            //aircraft current state === APPROACH
            if ($aircraft->getAircraftCurrentState() === 'APPROACH')
            {
                //approve LANDED notification intent for aircraft
                $savedIntent = $intRepository->processIntentRequestApproval($intent->getId(), "ACCEPTED");
                if ($savedIntent){
                    //update aircraft state to LANDED
                    $changedState = $craftRepository->changeAircraftState($aircraft->getId(), $_REQUEST['state']);
                    //
                    if ($changedState)
                        $response->setStatusCode(Response::HTTP_NO_CONTENT);
                    else
                        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                }
                else
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
            else{
                //An Aircraft already on approach to land, disapprove APPROACH for aircraft
                $savedIntent = $intRepository->processIntentRequestApproval($intent->getId(), "REJECTED");
                //
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
        }
        else
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);


        
        //
        return $response;
    }
}