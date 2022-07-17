<?php

namespace App\Repository;

use App\Entity\Intents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Intents>
 *
 * @method Intents|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intents|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intents[]    findAll()
 * @method Intents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intents::class);
    }

    public function add(Intents $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function update(Intents $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Intents $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllAircraftsIntents(): array
    {
        $entityManager = $this->getEntityManager();
        //generate query to execute
        $query = $entityManager->createQuery(
            'SELECT i.id, a.name,a.type,i.state,i.status,i.created,i.updated
             FROM App\Entity\Aircrafts a, App\Entity\Intents i
             WHERE a.id = i.aircraft 
             ORDER BY i.created DESC'
        );
        //return an array of all Aircrafts and their location data
        return $query->getResult();
    }

    public function processIntentRequestApproval(int $id, string $approvalStatus): bool
    {
        if (!empty($id) && is_numeric($id) && in_array($approvalStatus, ['ACCEPTED','REJECTED']))
        {
            $_updateIntent = $this->getEntityManager()->getRepository(Intents::class)->find($id);
            //
            if (!$_updateIntent)
            {
                return false;
            }
            //
            $_updateIntent->setStatus($approvalStatus);
            $_updateIntent->setUpdated(new \DateTime());
            //
            $this->getEntityManager()->flush();
            //
            return true;
        }
        else
            return false;
    }
}
