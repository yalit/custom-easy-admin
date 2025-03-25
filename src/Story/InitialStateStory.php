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
        UserFactory::createOne(UserFactory::admin());
        UserFactory::createMany(5, [UserFactory::class, 'publisher']);
        UserFactory::createMany(5, [UserFactory::class, 'author']);
        PostFactory::createMany(50, static function () {
            return [
                'tags' => TagFactory::randomRange(1, 5),
                'author' => UserFactory::random(),
            ];
        });
        CommentFactory::createMany(200);
    }
}
