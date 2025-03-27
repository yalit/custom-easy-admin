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
     * @param Proxy<User> $author
     * @return Proxy<Post>
     */
    public static function anyOwned(Proxy $author, PostStatus $status): Proxy
    {
        $allPosts = PostFactory::all();

        $posts = array_values(array_filter($allPosts, fn (/** @param Proxy<Post> $p */Proxy $p) => $p->getAuthor()->getId() === $author->getId() && $p->getStatus() === $status));
        if (count($posts) === 0){
            throw new Exception("Post not found");
        }

        return $posts[0];
    }

    /**
     * @param Proxy<User> $author
     * @return Proxy<Post>
     */
    public static function anyNotOwned(Proxy $author, PostStatus $status): Proxy
    {
        $allPosts = PostFactory::all();

        $post = array_values(array_filter($allPosts, fn (/** @param Proxy<Post> $p */Proxy $p) => $p->getAuthor()->getId() !== $author->getId() && $p->getStatus() === $status));
        if (count($post) === 0){
            throw new Exception("Post not found");
        }

        return $post[0];
    }

    public static function draft(int $nb = 10, UserRole $role = UserRole::AUTHOR): void
    {
        for ($i = 0; $i < $nb; $i++) {
            $author = PostFactory::getAuthor($role);
            $post = PostFactory::new()
                ->asDraft()
                ->by($author)
                ->with(['statusChanges' => [PostStatusChangeFactory::draftHistory($author)]])
                ->create();

            PostFactory::addCommentToPost($post);
        }
    }

    public static function inReview(int $nb = 10, UserRole $role = UserRole::AUTHOR): void
    {
        for ($i = 0; $i < $nb; $i++) {
            $author = PostFactory::getAuthor($role);

            $post = PostFactory::new()
                ->asInReview()
                ->by($author)
                ->with(['statusChanges' => PostStatusChangeFactory::inReviewHistory($author)])
                ->create();

            PostFactory::addCommentToPost($post);
        }
    }

    public static function published(int $nb = 10, UserRole $role = UserRole::AUTHOR): void
    {
        for ($i = 0; $i < $nb; $i++) {
            $author = PostFactory::getAuthor($role);
            $publisher = UserFactory::anyPublisher();

            $post = PostFactory::new()
                ->asPublished()
                ->by($author)
                ->with(['statusChanges' => PostStatusChangeFactory::inPublishHistory($author, $publisher)])
                ->create();

            PostFactory::addCommentToPost($post);
        }
    }

    public static function archived(int $nb = 10, UserRole $role = UserRole::AUTHOR): void
    {
        for ($i = 0; $i < $nb; $i++) {
            $author = PostFactory::getAuthor($role);
            $publisher = UserFactory::anyPublisher();

            $post = PostFactory::new()
                ->asArchived()
                ->by($author)
                ->with(['statusChanges' => PostStatusChangeFactory::inArchivedHistory($author, $publisher)])
                ->create();

            PostFactory::addCommentToPost($post);
        }
    }

    public static function rejected(int $nb = 10, UserRole $role = UserRole::AUTHOR): void
    {
        for ($i = 0; $i < $nb; $i++) {
            $author = PostFactory::getAuthor($role);
            $publisher = UserFactory::anyPublisher();

            $post = PostFactory::new()
                ->asRejected()
                ->by($author)
                ->with(['statusChanges' => PostStatusChangeFactory::inRejectedHistory($author, $publisher)])
                ->create();

            PostFactory::addCommentToPost($post);
        }
    }

    public function by(Proxy $author): self
    {
        return $this->with(['author' => $author]);
    }

    public function asDraft(): self
    {
        return $this->with([
                'status' => PostStatus::DRAFT
            ]
        );
    }

    public function asInReview(): self
    {
        return $this->with([
            'status' => PostStatus::IN_REVIEW,
        ]);
    }

    public function asPublished(): self
    {
        return $this->with([
            'status' => PostStatus::PUBLISHED,
        ]);
    }

    public function asArchived(): self
    {
        return $this->with([
            'status' => PostStatus::ARCHIVED,
        ]);
    }

    public function asRejected(): self
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
