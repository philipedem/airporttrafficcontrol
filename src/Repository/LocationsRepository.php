<?php

namespace App\Repository;

use App\Entity\Locations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Locations>
 *
 * @method Locations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Locations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Locations[]    findAll()
 * @method Locations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Locations::class);
    }

    public function add(Locations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Locations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllAircraftsLocations(): array
    {
        $entityManager = $this->getEntityManager();
        //generate query to execute
        $query = $entityManager->createQuery(
            'SELECT l.id, a.name,l.type,l.latitude,l.longitude,l.altitude,l.heading,l.created
             FROM App\Entity\Aircrafts a, App\Entity\Locations l
             WHERE a.id = l.aircraft 
             ORDER BY l.created DESC'
        );
        //return an array of all Aircrafts and their location data
        return $query->getResult();
    }
}
