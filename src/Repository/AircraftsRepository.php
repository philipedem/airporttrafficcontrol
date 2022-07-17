<?php

namespace App\Repository;

use App\Entity\Aircrafts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * @extends ServiceEntityRepository<Aircrafts>
 *
 * @method Aircrafts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aircrafts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aircrafts[]    findAll()
 * @method Aircrafts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AircraftsRepository extends ServiceEntityRepository
{
    //
    private $params;
    //
    public function __construct(ManagerRegistry $registry, ContainerBagInterface $envparams)
    {
        parent::__construct($registry, Aircrafts::class);
        $this->params = $envparams;
    }

    public function add(Aircrafts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Aircrafts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByCallSign($value): ?Aircrafts
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.callsign = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function isRunWayOccupied(): bool
    {
        $entityManager = $this->getEntityManager();
        //generate query to execute
        $query = $entityManager->createQuery(
            "SELECT a.name FROM App\Entity\Aircrafts a WHERE a.state IN ('TAKE-OFF','LANDED')"
        );
        //
        $result = $query->getResult();
        //
        $num_rows = (!is_null($result) ? sizeof($result) : 0);
        //
        return $num_rows > 0;
    }

    public function isAnAircraftOnApproachOrLanded(): bool
    {
        $entityManager = $this->getEntityManager();
        //generate query to execute
        $query = $entityManager->createQuery(
            "SELECT a.name FROM App\Entity\Aircrafts a WHERE a.state IN ('APPROACH','LANDED')"
        );
        //
        $result = $query->getResult();
        //
        $num_rows = (!is_null($result) ? sizeof($result) : 0);
        //
        return $num_rows > 0;
    }

    public function changeAircraftState(int $id, string $newstate): bool
    {
        if (!empty($id) && is_numeric($id) && in_array($newstate, ['PARKED','TAKE-OFF','AIRBORNE','APPROACH','LANDED']))
        {
            $_updateAircraftState = $this->getEntityManager()->getRepository(Aircrafts::class)->find($id);
            //
            if (!$_updateAircraftState)
            {
                return false;
            }
            //
            $_updateAircraftState->setAircraftCurrentState($newstate);
            //
            $this->getEntityManager()->flush();
            //
            return true;
        }
        else
            return false;
    }

    public function getParkedAircraftsQueryBuilder(string $type): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type = :craft_type')
            ->andWhere('a.state = :craft_state')
            ->setParameters([
                'craft_type'    => $type,
                'craft_state'   => 'PARKED'
            ]);
    }
    
    public function parkLandedAircrafts(): int
    {
        //Get collection/list of LANDED Aircrafts to be parked
        $landedAircrafts = $this->createQueryBuilder('a')
                                ->andWhere('a.state = :st')
                                ->setParameter('st', "LANDED")
                                ->getQuery()
                                ->getResult();
        //
        $airlinerSlots = $this->params->get('app.airliner_park_slots');
        $privateSlots = $this->params->get('app.private_park_slots');
        $parkedAircrafts = 0;
        //Check for parking slot availability for each type of aircraft to be PARKED
        foreach($landedAircrafts as $aircraft)
        {
            //Get type of Aircraft
            $parkingSlotAvailable = false;
            $aircraftType = $aircraft->getAircraftType();
            $aircraftTypeParkedCount = $this->getParkedAircraftsQueryBuilder($aircraftType)->select('COUNT(a.id)')->getQuery()->getSingleScalarResult();
            //
            if ($aircraftType === 'AIRLINER')
            {
                $parkingSlotAvailable = ($aircraftTypeParkedCount < $airlinerSlots ? true : false);
            }
            elseif ($aircraftType === 'PRIVATE')
            {
                $parkingSlotAvailable = ($aircraftTypeParkedCount < $privateSlots ? true : false);
            }
            //
            //Parking slot available for type of Aircraft
            if ($parkingSlotAvailable)
            {
                $_parkAircraft = $this->getEntityManager()->getRepository(Aircrafts::class)->find($aircraft->getId());
                //
                $_parkAircraft->setAircraftCurrentState('PARKED');
                //
                $this->getEntityManager()->flush();
                //
                $parkedAircrafts += 1;
            }
        }
        //
        return $parkedAircrafts;
    }
}
