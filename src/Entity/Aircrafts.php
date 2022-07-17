<?php

namespace App\Entity;

use App\Repository\AircraftsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AircraftsRepository::class)
 */
class Aircrafts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="aircraft_name", type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(name="aircraft_type", type="string", length=10)
     */
    private $type;

    /**
     * @ORM\Column(name="aircraft_capacity", type="integer", nullable=true)
     */
    private $capacity;

    /**
     * @ORM\Column(name="aircraft_call_sign", type="string", length=20, unique=true)
     */
    private $callsign;

    /**
     * @ORM\Column(name="aircraft_state", type="string", length=20)
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity=Locations::class, mappedBy="aircraft", orphanRemoval=true)
     */
    private $locations;

    /**
     * @ORM\OneToMany(targetEntity=Intents::class, mappedBy="aircraft", orphanRemoval=true)
     */
    private $intents;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->intents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAircraftName(): ?string
    {
        return $this->name;
    }

    public function setAircraftName(string $name): self
    {
        $this->name = $name;

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

    public function getAircraftCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setAircraftCapacity(?int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getAircraftCallSign(): ?string
    {
        return $this->callsign;
    }

    public function setAircraftCallSign(string $callsign): self
    {
        $this->callsign = $callsign;

        return $this;
    }

    public function getAircraftCurrentState(): ?string
    {
        return $this->state;
    }

    public function setAircraftCurrentState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, Locations>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Locations $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setAircraftId($this);
        }

        return $this;
    }

    public function removeLocation(Locations $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getAircraftId() === $this) {
                $location->setAircraftId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Intents>
     */
    public function getIntents(): Collection
    {
        return $this->intents;
    }

    public function addIntent(Intents $intent): self
    {
        if (!$this->intents->contains($intent)) {
            $this->intents[] = $intent;
            $intent->setAircraftId($this);
        }

        return $this;
    }

    public function removeIntent(Intents $intent): self
    {
        if ($this->intents->removeElement($intent)) {
            // set the owning side to null (unless already changed)
            if ($intent->getAircraftId() === $this) {
                $intent->setAircraftId(null);
            }
        }

        return $this;
    }
}
