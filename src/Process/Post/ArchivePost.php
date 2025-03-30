<?php

namespace App\Process\Post;

use App\Entity\Post;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
readonly class ArchivePost
{
    public function __construct(
        public Post $post,
    ) {}
}
