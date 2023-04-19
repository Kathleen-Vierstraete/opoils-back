<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\HobbyRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=HobbyRepository::class)
 */
class Hobby
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_hobbies_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"get_hobbies_collection"})
     */
    private $hobby;

    /**
     * @ORM\ManyToOne(targetEntity=Dog::class, inversedBy="hobbies")
     * @Groups({"get_hobbies_collection"})
     */
    private $dog;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHobby(): ?string
    {
        return $this->hobby;
    }

    public function setHobby(string $hobby): self
    {
        $this->hobby = $hobby;

        return $this;
    }

    public function getDog(): ?Dog
    {
        return $this->dog;
    }

    public function setDog(?Dog $dog): self
    {
        $this->dog = $dog;

        return $this;
    }

    public function __toString(){
        return $this->hobby; // Remplacer champ par une propriété "string" de l'entité
    }
}
