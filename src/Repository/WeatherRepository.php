<?php

namespace App\Repository;

use App\Entity\Weather;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @extends ServiceEntityRepository<Weather>
 *
 * @method Weather|null find($id, $lockMode = null, $lockVersion = null)
 * @method Weather|null findOneBy(array $criteria, array $orderBy = null)
 * @method Weather[]    findAll()
 * @method Weather[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeatherRepository extends ServiceEntityRepository
{
    private HttpClientInterface $httpClient;

    public function __construct(ManagerRegistry $registry, HttpClientInterface $httpClient)
    {
        parent::__construct($registry, Weather::class);
        $this->httpClient = $httpClient;
    }

    public function add(Weather $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Weather $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function fetchWeatherUpdates(): void
    {
        //Get latest weather update for the airport area
        $response = $this->httpClient->request(
            'GET',
            'https://api.github.com/repos/symfony/symfony-docs',
            [
                'query' => [
                    'lat'   => '5.606255658713315',
                    'lon'   => '-0.1681878757583262',
                    'appid' => '1a1f91e2241e9056cf2dd4f9cf66e8da'
                ],
            ]
        );
        //
        //Successfull update received
        if ($response->getStatusCode() == 200){
            //Save collected current weather update
            $content = $response->toArray();
            if (!empty($content)){
                $_weather = new Weather();
                $_weather->setDescription($content['weather']['description']);
                $_weather->setTemperature((float)$content['main']['temp']);
                $_weather->setVisibility($content['visibilty']);
                $_weather->setWind($content['wind']);
                $_weather->setLastUpdate(new \DateTime());
                //
                $this->add($_weather, true);

                //$this->getEntityManager()->persist($weather);
                //$this->getEntityManager()->flush();
            }
        }
    }

    public function getCurrentWeatherUpdate(): array
    {
        $entityManager = $this->getEntityManager();
        //generate query to execute
        $query = $entityManager->createQuery(
            'SELECT w.*
             FROM App\Entity\Weather w 
             ORDER BY w.last_update DESC
             LIMIT 1'
        );
        //return an array of all Aircrafts and their location data
        return $query->getResult();
    }
}
