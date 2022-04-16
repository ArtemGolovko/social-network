<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author")
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $posts;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, mappedBy="likes")
     */
    private $postsLiked;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="subscribed")
     */
    private $subscribers;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="subscribers")
     */
    private $subscribed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->postsLiked = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
        $this->subscribed = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPostsLiked(): Collection
    {
        return $this->postsLiked;
    }

    public function addPostsLiked(Post $postsLiked): self
    {
        if (!$this->postsLiked->contains($postsLiked)) {
            $this->postsLiked[] = $postsLiked;
            $postsLiked->addLike($this);
        }

        return $this;
    }

    public function removePostsLiked(Post $postsLiked): self
    {
        if ($this->postsLiked->removeElement($postsLiked)) {
            $postsLiked->removeLike($this);
        }

        return $this;
    }

    public function getAvatarUrl(): string
    {
        return 'build/img/user.jpg';
    }

    /**
     * @return Collection|self[]
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    public function addSubscriber(self $subscriber): self
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers[] = $subscriber;
        }

        return $this;
    }

    public function removeSubscriber(self $subscriber): self
    {
        $this->subscribers->removeElement($subscriber);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSubscribed(): Collection
    {
        return $this->subscribed;
    }

    public function addSubscribed(self $subscribed): self
    {
        if (!$this->subscribed->contains($subscribed)) {
            $this->subscribed[] = $subscribed;
            $subscribed->addSubscriber($this);
        }

        return $this;
    }

    public function removeSubscribed(self $subscribed): self
    {
        if ($this->subscribed->removeElement($subscribed)) {
            $subscribed->removeSubscriber($this);
        }

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
