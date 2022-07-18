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
    /* 
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    private const URL ='https://api.openweathermap.org/data/2.5/weather';

    private $weatherApiKey;

    public function __construct(ManagerRegistry $registry, HttpClientInterface $httpClient, $weatherApiKey)
    {
        parent::__construct($registry, Weather::class);
        $this->httpClient = $httpClient;
        $this->weatherApiKey = $weatherApiKey;
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

    public function fetchWeatherUpdates(): string
    {
        //Get latest weather update for the airport area
        $response = $this->httpClient->request(
            'GET',
            self::URL,
            [
                'query' => [
                    'lat'   => '5.606255658713315',
                    'lon'   => '-0.1681878757583262',
                    'appid' => $this->weatherApiKey
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
                $_weather->setDescription($content['weather'][0]['description']);
                $_weather->setTemperature((float)$content['main']['temp']);
                $_weather->setVisibility($content['visibility']);
                $_weather->setWind($content['wind']);
                $_weather->setLastUpdate(new \DateTime());
                //
                $this->add($_weather, true);
                //
                $_data = [
                    'description' => $content['weather'][0]['description'],
                    'temperature' => (float)$content['main']['temp'],
                    'visibility' => $content['visibility'],
                    'wind' => $content['wind']
                ];
                return json_encode($_data,JSON_FORCE_OBJECT);
            }
            //
            return "NO Weather data obtained";
        }
        //
        return "NO Weather data obtained";
    }

    public function getCurrentWeatherUpdate(): ?Weather
    {
        //generate query to execute
        return $this->createQueryBuilder('w')
            ->orderBy('w.last_update', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }
}
