<?php

namespace App\Story;

use App\Factory\CommentFactory;
use App\Factory\PostFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
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
    }
}
