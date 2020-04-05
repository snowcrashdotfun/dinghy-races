<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tournament", inversedBy="teams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tournament;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="teams")
     * @ORM\OrderBy({"username" = "ASC"})
     */
    private $members;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $points;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TournamentScore", mappedBy="team", cascade={"persist"})
     */
    private $scores;

    private $scores_available;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->scores = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->addTeam($this);
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
            // set the owning side to null (unless already changed)
            if ($member->getTeams()->contains($this)) {
                $member->removeTeam($this);
            }
        }

        return $this;
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

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): self
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return Collection|TournamentScore[]
     */
    public function getScores(): Collection
    {
        return $this->scores;
    }

    public function addScore(TournamentScore $score): self
    {
        if (!$this->scores->contains($score)) {
            $this->scores[] = $score;
            $score->setTeam($this);
        }

        return $this;
    }

    public function removeScore(TournamentScore $score): self
    {
        if ($this->scores->contains($score)) {
            $this->scores->removeElement($score);
            // set the owning side to null (unless already changed)
            if ($score->getTeam() === $this) {
                $score->setTeam(null);
            }
        }

        return $this;
    }

    public function setScoresAvailable(int $count): self
    {
        $this->scores_available = $count;

        return $this;
    }

    public function useScore(): self
    {
        $this->scores_available -= 1;

        return $this;
    }

    public function hasScoresAvailable(): bool
    {
        return ($this->scores_available > 0);
    }
}