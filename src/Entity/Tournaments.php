<?php

namespace App\Entity;

use App\Repository\TournamentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
    private Collection $Teams;

    #[ORM\OneToOne(mappedBy: 'tournament', cascade: ['persist', 'remove'])]
    private ?Bracket $bracket = null;



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
    


}
