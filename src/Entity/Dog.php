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
     * @Groups({"get_dogs_collection", "get_members_collection", "get_item", "get_dog_item", "get_connected_member"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"get_dogs_collection", "get_members_collection", "get_hobbies_collection", "get_pictures_collection", "get_item" , "get_dog_item", "get_connected_member"}) 
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2)
     * @Groups({"get_dogs_collection", "get_members_collection", "get_item" , "get_dog_item", "get_connected_member"})
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=35)
     * @Groups({"get_dogs_collection", "get_members_collection", "get_item", "get_dog_item", "get_connected_member"})
     */
    private $race;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get_dogs_collection", "get_members_collection", "get_item", "get_dog_item", "get_connected_member"})
     */
    private $presentation;

    /**
     * @ORM\ManyToOne(targetEntity=Member::class, inversedBy="dogs", cascade={"persist"})
     * @Groups({"get_dogs_collection"})
     */
    private $member;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="dog", cascade={"remove"})
     * @Groups({"get_connected_member", "get_dogs_collection"})
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity=Hobby::class, mappedBy="dog", cascade={"remove"})
     * @Groups({"get_connected_member", "get_dogs_collection"})
     */
    private $hobbies;

    /**
     * @ORM\Column(type="string", length=65, nullable=true)
     * @Groups({"get_dogs_collection", "get_members_collection", "get_connected_member"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"get_dogs_collection", "get_members_collection", "get_connected_member"})
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"get_dogs_collection", "get_members_collection", "get_connected_member"})
     */
    private $personality;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_dogs_collection", "get_members_collection", "get_hobbies_collection", "get_pictures_collection", "get_item" , "get_dog_item", "get_connected_member"}) 
     */
    private $main_picture;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getPersonality(): ?string
    {
        return $this->personality;
    }

    public function setPersonality(string $personality): self
    {
        $this->personality = $personality;

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->main_picture;
    }

    public function setMainPicture(string $main_picture): self
    {
        $this->main_picture = $main_picture;

        return $this;
    }
}
