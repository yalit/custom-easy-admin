<?php

namespace App\Process\Post;

use App\Entity\Enums\PostStatus;
use App\Factory\PostStatusChangeFactory;
use App\Repository\PostRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class RequestReviewHandler
{
    public function __construct(
        private PostRepository $postRepository,
        private PostStatusChangeFactory $postStatusChangeFactory,
    )
    {
    }

    public function __invoke(RequestReview $requestReview): void
    {
        $post = $requestReview->post;
        if ($post->getStatus() !== PostStatus::DRAFT) {
            return;
        }

        $post->setStatus(PostStatus::IN_REVIEW);
        $post->addStatusChange($this->postStatusChangeFactory->create(PostStatus::DRAFT, PostStatus::IN_REVIEW));
        $this->postRepository->save($post);
    }
}
