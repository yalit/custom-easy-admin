<?php

namespace App\Tests\Story;

use App\Story\Factory\UserFactory;

class FunctionalTestStory
{
    public static function oneUserOfEach(): array
    {
        return [
            'admin' => [UserFactory::anyAdmin()],
            'publisher' => [UserFactory::anyPublisher()],
            'author' => [UserFactory::anyAuthor()],
        ];
    }

    public static function noAdminUsers(): array
    {
        return [
            'publisher' => [UserFactory::anyPublisher()],
            'author' => [UserFactory::anyAuthor()],
        ];
    }
}
