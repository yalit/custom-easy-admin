<?php

namespace App\Tests\Trait;

use App\Entity\Enums\PostStatus;
use App\Entity\Enums\UserRole;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;

trait GetTestData
{
    protected function anyUser(UserRole $userRole):User
    {
        $users = $this->userRepository()->findAll();

        $usersWithRole = array_values(array_filter($users, fn(User $user) => $user->hasRole($userRole->value)));
        static::assertGreaterThanOrEqual(1, count($usersWithRole));

        return $usersWithRole[0];
    }

    protected function anyPost(PostStatus $postStatus):Post
    {
        $post = $this->postRepository()->findOneBy(['status' => $postStatus]);
        static::assertNotNull($post);

        return $post;
    }

    protected function anyPostOwned(User $user, PostStatus $postStatus):Post
    {
        $post = $this->postRepository()->findOneBy(['status' => $postStatus, 'author' => $user]);
        static::assertNotNull($post);

        return $post;
    }

    protected function anyPostNotOwned(User $user, PostStatus $postStatus): Post
    {
        $allPosts = $this->postRepository()->findBy(['status' => $postStatus]);

        $notOwnedPosts = array_values(array_filter($allPosts, fn(Post $post) => $post->getAuthor()->getId() !== $user->getId()));
        static::assertGreaterThanOrEqual(1, count($notOwnedPosts));

        return $notOwnedPosts[0];
    }

    protected function userRepository(): UserRepository
    {
        return static::getContainer()->get(UserRepository::class);
    }

    protected function postRepository():PostRepository
    {
        return static::getContainer()->get(PostRepository::class);
    }
}
