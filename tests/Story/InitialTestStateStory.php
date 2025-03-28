<?php

namespace App\Tests\Story;

use App\Entity\Enums\PostStatus;
use App\Entity\Enums\UserRole;
use App\Story\Factory\PostFactory;
use App\Story\Factory\TagFactory;
use App\Story\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class InitialTestStateStory extends Story
{
    public function build(): void
    {
        TagFactory::createMany(3);
        UserFactory::admin();
        UserFactory::anyPublisher();
        UserFactory::anyAuthor();
        PostFactory::manyForStatus(PostStatus::DRAFT, 1);
        PostFactory::manyForStatus(PostStatus::IN_REVIEW, 1);
        PostFactory::manyForStatus(PostStatus::PUBLISHED, 1);
        PostFactory::manyForStatus(PostStatus::ARCHIVED, 1);
        PostFactory::manyForStatus(PostStatus::DRAFT, 1, UserRole::ADMIN);
        PostFactory::manyForStatus(PostStatus::IN_REVIEW, 1, UserRole::ADMIN);
        PostFactory::manyForStatus(PostStatus::PUBLISHED, 1, UserRole::ADMIN);
        PostFactory::manyForStatus(PostStatus::ARCHIVED, 1, UserRole::ADMIN);
    }
}
