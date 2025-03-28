<?php

namespace App\Tests\Integration\Process\Post;

use App\Entity\Enums\PostStatus;
use App\Entity\Post;
use App\Process\Post\PostRequestReview;
use App\Process\Post\ProjectRequestReviewHandler;
use App\Process\Post\PublishPost;
use App\Process\Post\PublishPostHandler;
use App\Tests\Integration\AbstractAppKernelTestCase;
use App\Tests\Story\InitialTestStateStory;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Attribute\WithStory;

#[WithStory(InitialTestStateStory::class)]
class PublishPostHandlerTest extends AbstractAppKernelTestCase
{
    public static function nonInReviewPostStatus(): iterable
    {
        return [
            'Draft' => [PostStatus::DRAFT],
            'Published' => [PostStatus::PUBLISHED],
            'Archived' => [PostStatus::ARCHIVED],
            'Rejected' => [PostStatus::REJECTED],
        ];
    }

    public function testRequestReviewForInReview(): void
    {
        $post = $this->getPost(PostStatus::IN_REVIEW);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $requestReview = new PublishPost($post);

        $process = static::getContainer()->get(PublishPostHandler::class);
        $process($requestReview);

        self::assertEquals(PostStatus::PUBLISHED, $post->getStatus());
        self::assertCount($nbChanges + 1, $post->getStatusChanges());
        self::assertEquals(PostStatus::PUBLISHED, $post->getStatusChanges()->first()->getCurrentStatus());
        self::assertEquals(PostStatus::IN_REVIEW, $post->getStatusChanges()->first()->getPreviousStatus());
    }

    #[DataProvider('nonInReviewPostStatus')]
    public function testRequestReviewForNonDraft(PostStatus $status): void
    {
        $post = $this->getPost($status);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $currentStatus = $post->getStatus();
        $requestReview = new PublishPost($post);

        $process = static::getContainer()->get(PublishPostHandler::class);
        $process($requestReview);

        self::assertEquals($currentStatus, $post->getStatus());
        self::assertCount($nbChanges, $post->getStatusChanges());
    }


    private function getPost(PostStatus $status): Post
    {
        $postRepository = $this->entityManager->getRepository(Post::class);
        $post = $postRepository->findOneBy(['status' => $status]);
        self::assertNotNull($post);

        return $post;
    }
}
