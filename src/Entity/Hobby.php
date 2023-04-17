<?php

namespace App\Entity;

use App\Repository\HobbyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HobbyRepository::class)
 */
class Hobby
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $hobby;

    /**
     * @ORM\ManyToOne(targetEntity=Dog::class, inversedBy="hobbies")
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
