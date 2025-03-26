<?php

namespace App\Factory;

use App\Entity\Comment;
use App\Entity\Post;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Comment>
 */
final class CommentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Comment::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'author' => UserFactory::random(),
            'content' => self::faker()->text(),
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime('now')),
        ];
    }

    /**
     * @param Proxy<Post> $post
     */
    public function withPost(Proxy $post): self
    {
        return $this->with(['post' => $post]);
    }
}
