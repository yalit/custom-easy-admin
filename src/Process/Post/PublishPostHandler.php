<?php

namespace App\Process\Post;

use App\Entity\Enums\PostStatus;
use App\Factory\PostStatusChangeFactory;
use App\Repository\PostRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class PublishPostHandler
{
    public function __construct(
        private PostRepository $postRepository,
        private PostStatusChangeFactory $postStatusChangeFactory,
    ) {}

    public function __invoke(PublishPost $publishPost): void
    {
        $post = $publishPost->post;
        if ($post->getStatus() !== PostStatus::IN_REVIEW) {
            return;
        }

        $post->setStatus(PostStatus::PUBLISHED);
        $post->addStatusChange($this->postStatusChangeFactory->create(PostStatus::IN_REVIEW, PostStatus::PUBLISHED));
        $this->postRepository->save($post);
    }
}
