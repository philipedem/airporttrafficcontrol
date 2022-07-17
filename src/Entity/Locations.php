<?php

namespace App\Entity;

use App\Repository\LocationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocationsRepository::class)
 */
class Locations
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Aircrafts::class, inversedBy="locations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $aircraft;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $longitude;

    /**
     * @ORM\Column(type="integer")
     */
    private $altitude;

    /**
     * @ORM\Column(type="integer")
     */
    private $heading;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAircraftId(): ?Aircrafts
    {
        return $this->aircraft;
    }

    public function setAircraftId(?Aircrafts $aircraft): self
    {
        $this->aircraft = $aircraft;

        return $this;
    }

    public function getAircraftType(): ?string
    {
        return $this->type;
    }

    public function setAircraftType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAircraftLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setAircraftLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getAircraftLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setAircraftLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getAircraftAltitude(): ?int
    {
        return $this->altitude;
    }

    public function setAircraftAltitude(int $altitude): self
    {
        $this->altitude = $altitude;

        return $this;
    }

    public function getAircraftHeading(): ?int
    {
        return $this->heading;
    }

    public function setAircraftHeading(int $heading): self
    {
        $this->heading = $heading;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreatedAt(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }
}
