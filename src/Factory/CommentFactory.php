<?php

namespace App\Factory;

use App\Entity\Comment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

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
            'post' => PostFactory::random(),
            'author' => UserFactory::random(),
            'content' => self::faker()->text(),
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime('now')),
        ];
    }
}
