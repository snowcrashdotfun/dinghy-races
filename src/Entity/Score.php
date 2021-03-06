<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="score")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      "tournament" = "TournamentScore",
 *      "personal_best" = "PersonalBest"
 * })
 */
abstract class Score implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("public")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="bigint")
     * @Assert\NotBlank
     * @Groups("public")
     */
    private $points;

    /**
     * @ORM\Column(type="array")
     */
    private $points_history = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $videoUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $screenshot;

    protected $screenshot_file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $replay;

    protected $replay_file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups("public")
     */
    private $game;

    public function __construct()
    {
        $this->created_at = date_create('NOW');
        $this->updated_at = date_create('NOW');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

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

    public function getPointsHistory(): ?array
    {
        return $this->points_history;
    }

    public function setPointsHistory(array $points_history): self
    {
        $this->points_history = $points_history;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(?string $videoUrl): self
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    public function getScreenshot(): ?string
    {
        return $this->screenshot;
    }

    public function setScreenshot(?string $screenshot): self
    {
        $this->screenshot = $screenshot;

        return $this;
    }

    public function getScreenshotFile()
    {
        return $this->screenshot_file;
    }

    public function setScreenshotFile($file): self
    {
        $this->screenshot_file = $file;

        return $this;
    }

    public function getReplay(): ?string
    {
        return $this->replay;
    }

    public function setReplay(?string $replay): self
    {
        $this->replay = $replay;

        return $this;
    }

    public function getReplayFile()
    {
        return $this->replay_file;
    }

    public function setReplayFile($file): self
    {
        $this->replay_file = $file;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function serialize()
    {
        return '';
    }

    public function unserialize($serialized)
    {
        return;
    }
}