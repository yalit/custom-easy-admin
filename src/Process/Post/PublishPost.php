<?php

namespace App\Process\Post;

use App\Entity\Post;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
readonly class PublishPost
{
    public function __construct(
        public Post $post,
    ) {}
}
