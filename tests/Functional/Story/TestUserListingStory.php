<?php

namespace App\Tests\Functional\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class TestUserListingStory extends Story
{
    public function build(): void
    {
        UserFactory::createOne(UserFactory::admin());
        UserFactory::createMany(1, [UserFactory::class, 'publisher']);
        UserFactory::createMany(1, [UserFactory::class, 'author']);
    }
}
