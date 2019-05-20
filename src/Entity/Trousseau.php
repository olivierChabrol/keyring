<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use \JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrousseauRepository")
 */
class Trousseau implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modele;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="trousseaux")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateState;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $access;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $ticketIn;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $ticketOut;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSite(): ?int
    {
        return $this->site;
    }

    public function setSite(int $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): self
    {
        $this->modele = $modele;

        return $this;
    }
    

     public function jsonSerialize() {
        return [
            'modele' => $this->modele,
            'ref' => $this->ref,
            'site' => $this->site,
            'type' => $this->type,
            'access' => $this->access,
            'state' => $this->state,
            'creator' => $this->creator,
            'id' => $this->id

        ];
    }

     public function getCreator(): ?User
     {
         return $this->creator;
     }

     public function setCreator(?User $creator): self
     {
         $this->creator = $creator;

         return $this;
     }

     public function getState(): ?int
     {
         return $this->state;
     }

     public function setState(int $state): self
     {
         $this->state = $state;

         return $this;
     }

     public function getDateState(): ?\DateTimeInterface
     {
         return $this->dateState;
     }

     public function setDateState(?\DateTimeInterface $dateState): self
     {
         $this->dateState = $dateState;

         return $this;
     }

     public function getCreationDate(): ?\DateTimeInterface
     {
         return $this->creationDate;
     }

     public function setCreationDate(\DateTimeInterface $creationDate): self
     {
         $this->creationDate = $creationDate;

         return $this;
     }

     public function getAccess(): ?string
     {
         return $this->access;
     }

     public function setAccess(?string $access): self
     {
         $this->access = $access;

         return $this;
     }

     public function getTicketIn(): ?string
     {
         return $this->ticketIn;
     }

     public function setTicketIn(?string $ticketIn): self
     {
         $this->ticketIn = $ticketIn;

         return $this;
     }

     public function getTicketOut(): ?string
     {
         return $this->ticketOut;
     }

     public function setTicketOut(?string $ticketOut): self
     {
         $this->ticketOut = $ticketOut;

         return $this;
     }
}
