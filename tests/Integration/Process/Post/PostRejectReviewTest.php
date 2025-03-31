<?php

namespace App\Tests\Integration\Process\Post;


use App\Entity\Enums\PostStatus;
use App\Process\Post\PostRejectReview;
use App\Process\Post\PostRejectReviewHandler;
use App\Tests\Integration\AbstractAppKernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;


class PostRejectReviewTest extends AbstractAppKernelTestCase
{
    public function testRejectReviewForInReview(): void
    {
        $post = $this->anyPost(PostStatus::IN_REVIEW);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $postRejectReview = new PostRejectReview($post);

        $process = static::getContainer()->get(PostRejectReviewHandler::class);
        $process($postRejectReview);

        self::assertEquals(PostStatus::DRAFT, $post->getStatus());
        self::assertCount($nbChanges + 1, $post->getStatusChanges());
        self::assertEquals(PostStatus::DRAFT, $post->getStatusChanges()->first()->getCurrentStatus());
        self::assertEquals(PostStatus::IN_REVIEW, $post->getStatusChanges()->first()->getPreviousStatus());
    }

    public static function notInReviewPostStatus(): iterable
    {
        return [
            'Draft' => [PostStatus::DRAFT],
            'Published' => [PostStatus::PUBLISHED],
            'Archived' => [PostStatus::ARCHIVED],
        ];
    }

    #[DataProvider('notInReviewPostStatus')]
    public function testRejectReviewForNotInReview(PostStatus $status): void
    {
        $post = $this->anyPost($status);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $currentStatus = $post->getStatus();
        $postRejectReview = new PostRejectReview($post);

        $process = static::getContainer()->get(PostRejectReviewHandler::class);
        $process($postRejectReview);

        self::assertEquals($currentStatus, $post->getStatus());
        self::assertCount($nbChanges, $post->getStatusChanges());
    }
}
