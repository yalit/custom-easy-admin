<?php

namespace App\Factory;

use App\Entity\Post;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Post>
 */
final class PostFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Post::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        $title = self::faker()->numerify('Blog Post ###');
        $slug = (new AsciiSlugger())->slug($title);

        return [
            'title' => $title,
            'slug' => $slug,
            'summary' => self::faker()->paragraph(),
            'content' => implode("\n\n", self::faker()->paragraphs(5)),
            'author' => UserFactory::random(),
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-2 years', 'now')),
        ];
    }
}
