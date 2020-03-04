<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DraftEntryRepository")
 */
class DraftEntry
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Draft", inversedBy="draftEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $draft;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="draftEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(Draft $draft, User $user)
    {
        $this->created_at = new \DateTime('now');
        $this->draft = $draft;
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getDraft(): ?Draft
    {
        return $this->draft;
    }

    public function setDraft(?Draft $draft): self
    {
        $this->draft = $draft;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}