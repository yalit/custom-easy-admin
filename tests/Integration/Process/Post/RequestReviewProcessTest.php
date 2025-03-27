<?php

namespace App\Tests\Integration\Process\Post;

use App\Entity\Enums\PostStatus;
use App\Entity\Post;
use App\Process\Post\RequestReview;
use App\Process\Post\RequestReviewHandler;
use App\Tests\Integration\AbstractAppKernelTestCase;
use App\Tests\Story\InitialTestStateStory;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Attribute\WithStory;

#[WithStory(InitialTestStateStory::class)]
class RequestReviewProcessTest extends AbstractAppKernelTestCase
{
    public static function nonDraftPostStatus(): iterable
    {
        return [
            'In review' => [PostStatus::IN_REVIEW],
            'Published' => [PostStatus::PUBLISHED],
            'Archived' => [PostStatus::ARCHIVED],
            'Rejected' => [PostStatus::REJECTED],
        ];
    }

    public function testRequestReviewForDraft(): void
    {
        $post = $this->getPost(PostStatus::DRAFT);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $requestReview = new RequestReview($post);

        $process = static::getContainer()->get(RequestReviewHandler::class);
        $process($requestReview);

        self::assertEquals(PostStatus::IN_REVIEW, $post->getStatus());
        self::assertCount($nbChanges + 1, $post->getStatusChanges());
        self::assertEquals(PostStatus::IN_REVIEW, $post->getStatusChanges()->first()->getCurrentStatus());
        self::assertEquals(PostStatus::DRAFT, $post->getStatusChanges()->first()->getPreviousStatus());
    }

    #[DataProvider('nonDraftPostStatus')]
    public function testRequestReviewForNonDraft(PostStatus $status): void
    {
        $post = $this->getPost($status);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $currentStatus = $post->getStatus();
        $requestReview = new RequestReview($post);

        $process = static::getContainer()->get(RequestReviewHandler::class);
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
