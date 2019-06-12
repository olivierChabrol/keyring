<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use \JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StayRepository")
 */
class Stay implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $arrival;

    /**
     * @ORM\Column(type="datetime")
     */
    private $departure;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="stays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArrival(): ?\DateTimeInterface
    {
        return $this->arrival;
    }

    public function setArrival(\DateTimeInterface $arrival): self
    {
        $this->arrival = $arrival;

        return $this;
    }

    public function getDeparture(): ?\DateTimeInterface
    {
        return $this->departure;
    }

    public function setDeparture(\DateTimeInterface $departure): self
    {
        $this->departure = $departure;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'arrival' => $this->arrival== null?null:$this->arrival->format('d/m/Y'),
            'departure' => $this->departure== null?null:$this->departure->format('d/m/Y')
        ];
    }
}
