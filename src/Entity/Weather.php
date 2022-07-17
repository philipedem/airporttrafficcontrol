<?php

namespace App\Entity;

use App\Repository\WeatherRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WeatherRepository::class)
 */
class Weather
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $temperature;

    /**
     * @ORM\Column(type="float")
     */
    private $visibility;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $wind = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_update;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getVisibility(): ?float
    {
        return $this->visibility;
    }

    public function setVisibility(float $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getWind(): ?array
    {
        return $this->wind;
    }

    public function setWind(?array $wind): self
    {
        $this->wind = $wind;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->last_update;
    }

    public function setLastUpdate(?\DateTimeInterface $last_update): self
    {
        $this->last_update = $last_update;

        return $this;
    }
}
