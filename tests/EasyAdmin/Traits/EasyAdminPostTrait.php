<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Traits;

use App\Entity\Post;

trait EasyAdminPostTrait
{
    /**
     * @param string $status
     * @return Post
     */
    private function getPostWithStatus(string $status): Post
    {
        return $this->entityManager->getRepository(Post::class)->findOneBy(['status' => $status]);
    }
}