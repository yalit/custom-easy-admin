<?php

namespace App\Entity;

use App\Entity\Enums\PostStatus;
use App\Repository\PostStatusChangeRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity(repositoryClass: PostStatusChangeRepository::class)]
class PostStatusChange
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $time;

    #[ManyToOne(targetEntity: Post::class, inversedBy: 'statusChanges')]
    #[JoinColumn(nullable: true)]
    private ?Post $post = null;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: true)]
    private ?User $user = null;

    #[Column(type: Types::STRING, nullable: true, enumType: PostStatus::class)]
    private ?PostStatus $previousStatus = null;

    #[Column(type: Types::STRING, enumType: PostStatus::class)]
    private PostStatus $currentStatus = PostStatus::DRAFT;

    public function __construct()
    {
        $this->time = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return sprintf("%s->%s @ %s", $this->previousStatus->value, $this->currentStatus->value, $this->time->format('Y-m-d H:i:s'));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(DateTimeImmutable $time): void
    {
        $this->time = $time;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    public function getPreviousStatus(): PostStatus
    {
        return $this->previousStatus;
    }

    public function setPreviousStatus(PostStatus $previousStatus): void
    {
        $this->previousStatus = $previousStatus;
    }

    public function getCurrentStatus(): PostStatus
    {
        return $this->currentStatus;
    }

    public function setCurrentStatus(PostStatus $currentStatus): void
    {
        $this->currentStatus = $currentStatus;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
