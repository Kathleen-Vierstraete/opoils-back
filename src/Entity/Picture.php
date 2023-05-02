<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_pictures_collection", "get_item", "get_connected_member", "get_dogs_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"get_pictures_collection", "get_item", "get_connected_member", "get_dogs_collection"})
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity=Dog::class, inversedBy="pictures")
     * @Groups({"get_pictures_collection"})
     */
    private $dog;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

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
}
