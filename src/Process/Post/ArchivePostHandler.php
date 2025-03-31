<?php

namespace App\Process\Post;

use App\Entity\Enums\PostStatus;
use App\Factory\PostStatusChangeFactory;
use App\Repository\PostRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ArchivePostHandler
{
    public function __construct(
        private PostRepository $postRepository,
        private PostStatusChangeFactory $postStatusChangeFactory,
    )
    {
    }

    public function __invoke(ArchivePost $requestReview): void
    {
        $post = $requestReview->post;
        if ($post->getStatus() !== PostStatus::PUBLISHED) {
            return;
        }

        $post->setStatus(PostStatus::ARCHIVED);
        $post->addStatusChange($this->postStatusChangeFactory->create(PostStatus::PUBLISHED, PostStatus::ARCHIVED));
        $this->postRepository->save($post);
    }
}
