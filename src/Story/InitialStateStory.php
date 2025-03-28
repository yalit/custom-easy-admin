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
        PostFactory::manyDraft(5);
        PostFactory::manyInReview(5);
        PostFactory::manyPublished(5);
        PostFactory::manyArchived(5);
        PostFactory::rejected(5);
    }
}
