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
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="answers")
     */
    private $answerTo;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="answerTo")
     */
    private $answers;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
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

    public function getAnswerTo(): ?self
    {
        return $this->answerTo;
    }

    public function setAnswerTo(?self $answerTo): self
    {
        $this->answerTo = $answerTo;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(self $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setAnswerTo($this);
        }

        return $this;
    }

    public function removeAnswer(self $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getAnswerTo() === $this) {
                $answer->setAnswerTo(null);
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
