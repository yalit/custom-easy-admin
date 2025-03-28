<?php

namespace App\Story\Factory;

use App\Entity\Enums\PostStatus;
use App\Entity\Enums\UserRole;
use App\Entity\Post;
use App\Entity\User;
use Exception;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;

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
            'author' => UserFactory::anyAuthor(),
            'status' => PostStatus::DRAFT,
            'tags' => TagFactory::randomRange(0, 3),
        ];
    }

    /**
     * @return Proxy<Post>[]
     */
    public static function manyForStatus(PostStatus $status, int $nb = 10, UserRole $role = UserRole::AUTHOR): array
    {
        $posts = [];
        for ($i = 0; $i < $nb; $i++) {
            $posts[] = PostFactory::anyPost($status, $role);
        }
        return $posts;
    }

    /**
     * @return Proxy<Post>
     */
    public static function anyPost(PostStatus $status, UserRole $role = UserRole::AUTHOR, ?Proxy $author = null): Proxy
    {
        $allPosts = PostFactory::all();
        $posts = array_values(array_filter(
                $allPosts,
                fn(/** @param Proxy<Post> $p */ Proxy $p) => $p->getStatus() === $status
                    && (!$author || $p->getAuthor()->getId() === $author->getId())
            )
        );
        if (count($posts) !== 0) {
            return $posts[0];
        }

        return match ($status) {
            PostStatus::DRAFT => PostFactory::draft($role, $author),
            PostStatus::PUBLISHED => PostFactory::published($role, $author),
            PostStatus::IN_REVIEW => PostFactory::inReview($role, $author),
            PostStatus::ARCHIVED => PostFactory::archived($role, $author),
        };
    }

    /**
     * @param Proxy<User> $author
     * @return Proxy<Post>
     */
    public static function anyOwned(Proxy $author, PostStatus $status): Proxy
    {
        return self::anyPost($status, author: $author);
    }

    /**
     * @param Proxy<User> $author
     * @return Proxy<Post>
     */
    public static function anyNotOwned(Proxy $author, PostStatus $status): Proxy
    {
        $authors = UserFactory::all();
        $notAuthor = array_values(array_filter($authors, fn(/** @var Proxy<User> $user */Proxy $user) => $user->getId() !== $author->getId()))[0];
        return self::anyPost($status, author: $notAuthor);
    }

    /**
     * @param Proxy<User> | null $author
     * @return Proxy<Post>
     */
    public static function draft(UserRole $role = UserRole::AUTHOR, ?Proxy $author = null): Proxy
    {
        $author = $author ?? PostFactory::getAuthor($role);
        $post = PostFactory::new()
            ->asDraft()
            ->by($author)
            ->with(['statusChanges' => [PostStatusChangeFactory::draftHistory($author)]])
            ->create();

        PostFactory::addCommentToPost($post);

        return $post;
    }

    /**
     * @param Proxy<User> | null $author
     * @return Proxy<Post>
     */
    public static function inReview(UserRole $role = UserRole::AUTHOR, ?Proxy $author = null): Proxy
    {
        $author = $author ?? PostFactory::getAuthor($role);
        $post = PostFactory::new()
            ->asInReview()
            ->by($author)
            ->with(['statusChanges' => PostStatusChangeFactory::inReviewHistory($author)])
            ->create();

        PostFactory::addCommentToPost($post);

        return $post;
    }

    /**
     * @param Proxy<User> | null $author
     * @return Proxy<Post>
     */
    public static function published(UserRole $role = UserRole::AUTHOR, ?Proxy $author = null): Proxy
    {
        $author = $author ?? PostFactory::getAuthor($role);
        $publisher = UserFactory::anyPublisher();
        $post = PostFactory::new()
            ->asPublished()
            ->by($author)
            ->with(['statusChanges' => PostStatusChangeFactory::inPublishHistory($author, $publisher)])
            ->create();

        PostFactory::addCommentToPost($post);

        return $post;
    }

    /**
     * @param Proxy<User> | null $author
     * @return Proxy<Post>
     */
    public static function archived(UserRole $role = UserRole::AUTHOR, ?Proxy $author = null): Proxy
    {
        $author = $author ?? PostFactory::getAuthor($role);
        $publisher = UserFactory::anyPublisher();
        $post = PostFactory::new()
            ->asArchived()
            ->by($author)
            ->with(['statusChanges' => PostStatusChangeFactory::inArchivedHistory($author, $publisher)])
            ->create();

        PostFactory::addCommentToPost($post);

        return $post;
    }

    private function by(Proxy $author): self
    {
        return $this->with(['author' => $author]);
    }

    private function asDraft(): self
    {
        return $this->with([
                'status' => PostStatus::DRAFT
            ]
        );
    }

    private function asInReview(): self
    {
        return $this->with([
            'status' => PostStatus::IN_REVIEW,
        ]);
    }

    private function asPublished(): self
    {
        return $this->with([
            'status' => PostStatus::PUBLISHED,
        ]);
    }

    private function asArchived(): self
    {
        return $this->with([
            'status' => PostStatus::ARCHIVED,
        ]);
    }

    private function asRejected(): self
    {
        return $this->with([
            'status' => PostStatus::REJECTED,
        ]);
    }

    /**
     * @return Proxy<User>
     */
    private static function getAuthor(UserRole $role): Proxy
    {
        return match ($role) {
            UserRole::AUTHOR => UserFactory::anyAuthor(),
            UserRole::ADMIN => UserFactory::anyAdmin(),
            default => throw new Exception("Author can only be an author or an admin...")
        };
    }

    /**
     * @param Proxy<Post> $post
     */
    private static function addCommentToPost(Proxy $post, int $nb = 3): void
    {
        CommentFactory::new()
            ->withPost($post)
            ->many(rand(0, $nb))
            ->create();
    }
}
