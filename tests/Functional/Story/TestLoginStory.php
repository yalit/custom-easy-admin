<?php

namespace App\Tests\Functional\Story;

use App\Factory\CommentFactory;
use App\Factory\PostFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class TestLoginStory extends Story
{
    public function build(): void
    {
        UserFactory::createOne(UserFactory::admin());
        UserFactory::createMany(1, [UserFactory::class, 'publisher']);
        UserFactory::createMany(1, [UserFactory::class, 'author']);
    }
}
