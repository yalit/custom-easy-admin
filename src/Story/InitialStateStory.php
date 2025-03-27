<?php

namespace App\Story;

use App\Story\Factory\PostFactory;
use App\Story\Factory\TagFactory;
use App\Story\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class InitialStateStory extends Story
{
    public function build(): void
    {
        TagFactory::createMany(10);
        UserFactory::admin();
        UserFactory::publisher();
        UserFactory::author();
        PostFactory::draft(5);
        PostFactory::inReview(5);
        PostFactory::published(5);
        PostFactory::archived(5);
        PostFactory::rejected(5);
    }
}
