<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MemberRepository::class)
 */
class Member implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=5)
     * @Groups({"get_members_collection", "get_dogs_collection", "get_item", "get_member_item"})
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=35)
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_members_collection", "get_item", "get_member_item"})
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity=Dog::class, mappedBy="member", cascade={"remove"})
     * @Groups({"get_members_collection"})
     */
    private $dogs;

    /**
     * @ORM\Column(type="string", length=35)
     * @Groups({"get_members_collection", "get_dogs_collection"})
     */
    private $Pseudo;

    public function __construct()
    {
        $this->dogs = new ArrayCollection();
    }

    public function getId(): ?int
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
        // A VOIR L'AFFICHAGE DE L'USERNAME PAR APPORT A L'EMAIL ET AU CHOIX DE L'IDENTIFIANT DE CONNEXION
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
        // A VOIR L'AFFICHAGE DE L'USERNAME PAR APPORT A L'EMAIL ET AU CHOIX DE L'IDENTIFIANT DE CONNEXION
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, dog>
     */
    public function getDogs(): Collection
    {
        return $this->dogs;
    }

    public function addDog(dog $dog): self
    {
        if (!$this->dogs->contains($dog)) {
            $this->dogs[] = $dog;
            $dog->setMember($this);
        }

        return $this;
    }

    public function removeDog(dog $dog): self
    {
        if ($this->dogs->removeElement($dog)) {
            // set the owning side to null (unless already changed)
            if ($dog->getMember() === $this) {
                $dog->setMember(null);
            }
        }

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->Pseudo;
    }

    public function setPseudo(string $Pseudo): self
    {
        $this->Pseudo = $Pseudo;

        return $this;
    }
}
