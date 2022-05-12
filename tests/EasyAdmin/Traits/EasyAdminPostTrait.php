<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Traits;

use App\Entity\Post;
use App\Entity\User;

trait EasyAdminPostTrait
{
    private function getPostWithStatus(string $status): Post
    {
        return $this->entityManager->getRepository(Post::class)->findOneBy(['status' => $status]);
    }

    private function getPostWithStatusAndAuthor(string $status, User $author): Post
    {
        $dbAuthor = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $author->getUsername()]);
        return $this->entityManager->getRepository(Post::class)->findOneBy(['status' => $status, 'author' => $dbAuthor]);
    }

    private function getPostWithStatusAndNotAuthor(string $status, User $author): ?Post
    {
        /** @var Array<Post> $posts */
        $posts = $this->entityManager->getRepository(Post::class)->findBy(['status' => $status]);

        for($i = 0; $i < count($posts); $i++) {
            $post = $posts[$i];
            if ($post->getAuthor()->getUsername() !== $author->getUsername()) {
                break;
            }
        }

        return $post ?? null;
    }
}