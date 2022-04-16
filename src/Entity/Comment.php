<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    use TimestampableEntity;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="replays")
     */
    private $replayTo;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="replayTo")
     */
    private $replays;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    public function __construct()
    {
        $this->replays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getReplayTo(): ?self
    {
        return $this->replayTo;
    }

    public function setReplayTo(?self $replayTo): self
    {
        $this->replayTo = $replayTo;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getReplays(): Collection
    {
        return $this->replays;
    }

    public function addReplay(self $replay): self
    {
        if (!$this->replays->contains($replay)) {
            $this->replays[] = $replay;
            $replay->setReplayTo($this);
        }

        return $this;
    }

    public function removeReplay(self $replay): self
    {
        if ($this->replays->removeElement($replay)) {
            // set the owning side to null (unless already changed)
            if ($replay->getReplayTo() === $this) {
                $replay->setReplayTo(null);
            }
        }

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
