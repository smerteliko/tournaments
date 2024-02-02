<?php

namespace App\Entity;

use App\Repository\MatchesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MatchesRepository::class)]
class Matches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'matches',)]
    private ?Round $rounds = null;

    #[Assert\Count(['min'=>2])]
    #[ORM\ManyToMany(targetEntity: Teams::class, inversedBy: 'matches')]
    private Collection $teams;


    public function __construct()
    {
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRounds(): ?Round
    {
        return $this->rounds;
    }

    public function setRounds(?Round $rounds): static
    {
        $this->rounds = $rounds;

        return $this;
    }

    /**
     * @return Collection<int, Teams>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Teams $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
        }

        return $this;
    }

    public function removeTeam(Teams $team): static
    {
        $this->teams->removeElement($team);

        return $this;
    }

}
