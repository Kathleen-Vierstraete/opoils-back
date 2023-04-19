<?php

namespace App\Entity;

use App\Repository\DogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=DogRepository::class)
 */
class Dog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_dogs_collection", "get_item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"get_dogs_collection", "get_members_collection", "get_hobbies_collection", "get_pictures_collection", "get_item" }) 
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"get_dogs_collection", "get_item"})
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=35)
     * @Groups({"get_dogs_collection", "get_item"})
     */
    private $race;

    /**
     * @ORM\Column(type="text")
     * @Groups({"get_dogs_collection", "get_item"})
     */
    private $presentation;

    /**
     * @ORM\ManyToOne(targetEntity=Member::class, inversedBy="dogs", cascade={"persist"})
     * @Groups({"get_dogs_collection"})
     */
    private $member;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="dog", cascade={"remove"})
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity=Hobby::class, mappedBy="dog", cascade={"remove"})
     */
    private $hobbies;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->hobbies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(string $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return Collection<int, picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setDog($this);
        }

        return $this;
    }

    public function removePicture(picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getDog() === $this) {
                $picture->setDog(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, hobby>
     */
    public function getHobbies(): Collection
    {
        return $this->hobbies;
    }

    public function addHobby(hobby $hobby): self
    {
        if (!$this->hobbies->contains($hobby)) {
            $this->hobbies[] = $hobby;
            $hobby->setDog($this);
        }

        return $this;
    }

    public function removeHobby(hobby $hobby): self
    {
        if ($this->hobbies->removeElement($hobby)) {
            // set the owning side to null (unless already changed)
            if ($hobby->getDog() === $this) {
                $hobby->setDog(null);
            }
        }

        return $this;
    }
}
