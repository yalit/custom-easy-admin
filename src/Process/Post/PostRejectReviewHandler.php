<?php

namespace App\Process\Post;

use App\Entity\Enums\PostStatus;
use App\Factory\PostStatusChangeFactory;
use App\Repository\PostRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class PostRejectReviewHandler
{
    public function __construct(
        private PostRepository $postRepository,
        private PostStatusChangeFactory $postStatusChangeFactory,
    )
    {
    }

    public function __invoke(PostRejectReview $requestReview): void
    {
        $post = $requestReview->post;
        if ($post->getStatus() !== PostStatus::IN_REVIEW) {
            return;
        }

        $post->setStatus(PostStatus::DRAFT);
        $post->addStatusChange($this->postStatusChangeFactory->create(PostStatus::IN_REVIEW, PostStatus::DRAFT));
        $this->postRepository->save($post);
    }
}
