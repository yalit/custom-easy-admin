<?php

namespace App\Factory;

use App\Entity\Enums\PostStatus;
use App\Entity\Post;
use App\Entity\PostStatusChange;
use App\Entity\User;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;

/**
 * @extends PersistentProxyObjectFactory<PostStatusChange>
 */
final class PostStatusChangeFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return PostStatusChange::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'time' => self::faker()->dateTimeBetween('-30 days', "now"),
        ];
    }

    /**
     * @param Proxy<User> $author
     * @return Proxy<PostStatusChange>
     */
    public static function draftHistory(Proxy $author, ?DateTimeImmutable $date = null): Proxy
    {
        return PostStatusChangeFactory::new()
            ->by($author)
            ->onTime($date ?? self::faker()->dateTimeBetween('-30 days', "now"))
            ->asDraft()
            ->create();
    }

    /**
     * @param Proxy<User> $author
     * @return Proxy<PostStatusChange>[]
     */
    public static function inReviewHistory(Proxy $author, ?DateTimeImmutable $date = null): array
    {
        $date = $date ?? self::faker()->dateTimeBetween('-30 days', "-5 days");
        return [
            PostStatusChangeFactory::new()
                ->by($author)
                ->onTime($date)
                ->asDraft()
                ->create(),
            PostStatusChangeFactory::new()
                ->by($author)
                ->onTime($date->add(new DateInterval("P1D")))
                ->asInReview()
                ->create(),
        ];
    }

    /**
     * @param Proxy<User> $author
     * @return Proxy<PostStatusChange>[]
     */
    public static function inPublishHistory(Proxy $author, Proxy $publisher, ?DateTimeImmutable $date = null): array
    {
        $date = $date ?? self::faker()->dateTimeBetween('-30 days', "-5 days");
        return [
            PostStatusChangeFactory::new()
                ->by($author)
                ->onTime($date)
                ->asDraft()
                ->create(),
            PostStatusChangeFactory::new()
                ->by($author)
                ->onTime($date->add(new DateInterval("P1D")))
                ->asInReview()
                ->create(),
            PostStatusChangeFactory::new()
                ->by($publisher)
                ->onTime($date->add(new DateInterval("P2D")))
                ->asPublished()
                ->create(),
        ];
    }

    /**
     * @param Proxy<Post> $post
     */
    public function for(Proxy $post): self
    {
        return $this->with(['post' => $post]);
    }

    public function onTime(DateTimeInterface $time): self
    {
        return $this->with(['time' => $time]);
    }

    /**
     * @param Proxy<User> $user
     */
    public function by(Proxy $user): self
    {
        return $this->with(['user' => $user]);
    }

    public function asDraft(): self
    {
        return $this->with([
            'currentStatus' => PostStatus::DRAFT,
        ]);
    }

    public function asInReview(): self
    {
        return $this->with([
            'previousStatus' => PostStatus::DRAFT,
            'currentStatus' => PostStatus::IN_REVIEW,
        ]);
    }

    public function asPublished(): self
    {
        return $this->with([
            'previousStatus' => PostStatus::IN_REVIEW,
            'currentStatus' => PostStatus::PUBLISHED,
        ]);
    }

    public function asRejected(): self
    {
        return $this->with([
            'previousStatus' => PostStatus::IN_REVIEW,
            'currentStatus' => PostStatus::DRAFT,
        ]);
    }
}
