<?php

namespace App\Entity;

use App\Entity\Enums\PostStatus;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    /**
     * @var Collection<int, PostStatusChange>
     */
    #[ORM\OneToMany(targetEntity: PostStatusChange::class, mappedBy: 'post', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $statusChanges;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
    private Collection $tags;

    #[ORM\Column(type: Types::STRING, enumType: PostStatus::class)]
    private PostStatus $status = PostStatus::DRAFT;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->statusChanges = new ArrayCollection();
    }

    public function getStatusDate(): DateTimeImmutable
    {
        return $this->statusChanges->first()->getTime();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->statusChanges->last()->getTime();
    }

    public function getLatestStatusChange(): PostStatusChange
    {
        return $this->statusChanges->first();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getStatus(): PostStatus
    {
        return $this->status;
    }

    public function setStatus(PostStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Collection<int, PostStatusChange>
     */
    public function getStatusChanges(): Collection
    {
        $statusChangesArray = $this->statusChanges->toArray();
        uasort($statusChangesArray, fn (PostStatusChange $a, PostStatusChange $b) => $b->getTime()->getTimestamp() - $a->getTime()->getTimestamp());

        return new ArrayCollection($statusChangesArray);
    }

    public function addStatusChange(PostStatusChange $statusChange): static
    {
        if (!$this->statusChanges->contains($statusChange)) {
            $this->statusChanges->add($statusChange);
            $statusChange->setPost($this);
        }

        return $this;
    }

    public function removeStatusChange(PostStatusChange $statusChange): static
    {
        if ($this->statusChanges->removeElement($statusChange)) {
            // set the owning side to null (unless already changed)
            if ($statusChange->getPost() === $this) {
                $statusChange->setPost(null);
            }
        }

        return $this;
    }

}
