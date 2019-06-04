<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use \JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields= {"email"},
 * message= "l'email existe deja"
 * )
 */
class User implements UserInterface, JsonSerializable
{
/**
 * @ORM\Id()
 * @ORM\GeneratedValue()
 * @ORM\Column(type="integer")
 */
private $id;

/**
 * @ORM\Column(type="string", length=255)
 * @Assert\Email()
 */
private $email;

/**
 * @ORM\Column(type="string", length=255)
 */
private $username;

/**
 * @ORM\Column(type="string", length=255)
 * @Assert\Length(min="8", minMessage="Min 8 caractéres")
 */
private $password;

/**
 * @Assert\EqualTo(propertyPath="password", message="Vous devez taper le meme mot de passe")
 */
public $confirm_password;

/**
 * @ORM\Column(type="string", length=255)
 */
private $name;

/**
 * @ORM\Column(type="string", length=255)
 */
private $firstName;

/**
 * @ORM\Column(type="string", length=255, nullable=true)
 */
private $note;

/**
 * @ORM\OneToMany(targetEntity="App\Entity\Pret", mappedBy="user", orphanRemoval=true)
 */
private $prets;

/**
 * @ORM\OneToMany(targetEntity="App\Entity\Trousseau", mappedBy="creator")
 */
private $trousseaux;


/**
 * @ORM\Column(type="string", length=255)
 */
private $origine;


/**
* @var array
*
* @ORM\Column(type="json_array")
*/
private $roles = [];

/**
 * @ORM\Column(type="string", length=255, nullable=true)
 */
private $financement;

/**
 * @ORM\Column(type="integer")
 */
private $equipe;

/**
 * @ORM\Column(type="integer", nullable=true)
 */
private $position;

/**
 * @ORM\Column(type="string", length=5, nullable=true)
 */
private $nationality;

/**
 * @ORM\ManyToOne(targetEntity="App\Entity\User")
 */
private $host;

/**
 * @ORM\Column(type="datetime", nullable=true)
 */
private $arrival;

/**
 * @ORM\Column(type="datetime", nullable=true)
 */
private $departure;


public function __construct()
{
    $this->prets = new ArrayCollection();
    $this->trousseaux = new ArrayCollection();
}

public function getId()
 {
return $this->id;
 }

public function getEmail(): ?string
 {
return $this->email;
 }

public function setEmail(string $email): self
{
$this->email = $email;

return $this;
 }

public function getUsername(): ?string
 {
return $this->username;
 }

public function setUsername(string $username): self
{
$this->username = $username;

return $this;
 }

public function getPassword(): ?string
 {
return $this->password;
 }

public function setPassword(string $password): self
{
$this->password = $password;

return $this;
 }

public function eraseCredentials()
 {
// TODO: Implement eraseCredentials() method.
}

public function getSalt()
 {
// TODO: Implement getSalt() method.
}

public function getRoles()
 { 
	 $roles = $this->roles;

        // Afin d'être sûr qu'un user a toujours au moins 1 rôle
        if (empty($roles)) {
            $roles = array('ROLE_USER');
        }

        return array_unique($roles);
}

public function getName(): ?string
{
    return $this->name;
}

public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}

public function getFirstName(): ?string
{
    return $this->firstName;
}

public function setFirstName(string $firstName): self
{
    $this->firstName = $firstName;

    return $this;
}

public function getNote(): ?string
{
    return $this->note;
}

public function setNote(?string $note): self
{
    $this->note = $note;

    return $this;
}

/**
 * @return Collection|Pret[]
 */
public function getPrets(): Collection
{
    return $this->prets;
}

public function addPret(Pret $pret): self
{
    if (!$this->prets->contains($pret)) {
        $this->prets[] = $pret;
        $pret->setUser($this);
    }

    return $this;
}

public function removePret(Pret $pret): self
{
    if ($this->prets->contains($pret)) {
        $this->prets->removeElement($pret);
        // set the owning side to null (unless already changed)
        if ($pret->getUser() === $this) {
            $pret->setUser(null);
        }
    }

    return $this;
}

/**
 * @return Collection|Trousseau[]
 */
public function getTrousseaux(): Collection
{
    return $this->trousseaux;
}

public function addTrousseaux(Trousseau $trousseaux): self
{
    if (!$this->trousseaux->contains($trousseaux)) {
        $this->trousseaux[] = $trousseaux;
        $trousseaux->setCreator($this);
    }

    return $this;
}

public function removeTrousseaux(Trousseau $trousseaux): self
{
    if ($this->trousseaux->contains($trousseaux)) {
        $this->trousseaux->removeElement($trousseaux);
        // set the owning side to null (unless already changed)
        if ($trousseaux->getCreator() === $this) {
            $trousseaux->setCreator(null);
        }
    }

    return $this;
}


public function getOrigine(): ?string
{
    return $this->origine;
}

public function setOrigine(string $origine): self
{
    $this->origine = $origine;

    return $this;
}

public function setRoles(array $roles): void
{
	$this->roles = $roles;
}

    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'origine' => $this->origine,
            'note' => $this->note,
            'firstName' => $this->firstName,
            'id' => $this->id,
            'email' => $this->email
        ];
    }

    public function getFinancement(): ?string
    {
        return $this->financement;
    }

    public function setFinancement(?string $financement): self
    {
        $this->financement = $financement;

        return $this;
    }

    public function getEquipe(): ?string
    {
        return $this->equipe;
    }

    public function setEquipe(?string $equipe): self
    {
        $this->equipe = $equipe;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getHost(): ?self
    {
        return $this->host;
    }

    public function setHost(?self $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getArrival(): ?\DateTimeInterface
    {
        return $this->arrival;
    }

    public function setArrival(?\DateTimeInterface $arrival): self
    {
        $this->arrival = $arrival;

        return $this;
    }

    public function getDeparture(): ?\DateTimeInterface
    {
        return $this->departure;
    }

    public function setDeparture(?\DateTimeInterface $departure): self
    {
        $this->departure = $departure;

        return $this;
    }
}
