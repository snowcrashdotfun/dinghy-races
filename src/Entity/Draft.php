<?php

namespace App\Entity;

use App\Repository\DraftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DraftRepository::class)
 */
class Draft
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="guid", nullable=true, unique=true)
     */
    private $invite_token;

    /**
     * @ORM\OneToOne(targetEntity=Tournament::class, inversedBy="draft")
     */
    private $tournament;

    /**
     * @ORM\OneToMany(targetEntity=DraftEntry::class, mappedBy="draft", cascade={"persist", "remove"})
     * @ORM\OrderBy({"created_at" = "DESC"})
     */
    private $draftEntries;

    public function __construct()
    {
        $this->draftEntries = new ArrayCollection();
        $this->invite_token = bin2hex(random_bytes(8));
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInviteToken(): ?string
    {
        return $this->invite_token;
    }

    public function setInviteToken(?string $invite_token): self
    {
        $this->invite_token = $invite_token;

        return $this;
    }

    public function hasEntered(User $user)
    {
        return $this->getTournament()->getUsers()->exists(function($i, $entry) use ($user) {
            return $entry->getUser() === $user;
        });
    }

    /**
     * @return Collection|DraftEntry[]
     */
    public function getDraftEntries(): Collection
    {
        return $this->draftEntries;
    }

    public function addDraftEntry(DraftEntry $draftEntry): self
    {
        if (!$this->draftEntries->contains($draftEntry)) {
            $this->draftEntries[] = $draftEntry;
            $draftEntry->setDraft($this);
        }

        return $this;
    }

    public function removeDraftEntry(DraftEntry $draftEntry): self
    {
        if ($this->draftEntries->contains($draftEntry)) {
            $this->draftEntries->removeElement($draftEntry);
            // set the owning side to null (unless already changed)
            if ($draftEntry->getDraft() === $this) {
                $draftEntry->setDraft(null);
            }
        }

        return $this;
    }
}
