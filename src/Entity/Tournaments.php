<?php

namespace App\Entity;

use App\Repository\TournamentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TournamentsRepository::class)]
class Tournaments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\ManyToMany(targetEntity: Teams::class, inversedBy: 'tournaments')]
    #[Assert\Count(['min'=>1])]
    private Collection $Teams;

    #[ORM\OneToOne(mappedBy: 'tournament', cascade: ['persist', 'remove'])]
    private ?Bracket $bracket = null;

    #[ORM\Column(type: Types::ASCII_STRING)]
    private $slug = null;



    public function __construct()
    {
        $this->Teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    /**
     * @return Collection<int, Teams>
     */
    public function getTeams(): Collection
    {
        return $this->Teams;
    }

    public function addTeam(Teams $team): static
    {
        if (!$this->Teams->contains($team)) {
            $this->Teams->add($team);
        }

        return $this;
    }

    public function removeTeam(Teams $team): static
    {
        $this->Teams->removeElement($team);

        return $this;
    }

    public function getBracket(): ?Bracket
    {
        return $this->bracket;
    }

    public function setBracket(Bracket $bracket): static
    {
        // set the owning side of the relation if necessary
        if ($bracket->getTournament() !== $this) {
            $bracket->setTournament($this);
        }

        $this->bracket = $bracket;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug): static
    {
        $this->slug = $slug;

        return $this;
    }
    


}
