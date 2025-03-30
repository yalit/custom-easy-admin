<?php

namespace App\Tests\Integration\Process\Post;

use App\Entity\Enums\PostStatus;
use App\Process\Post\PostRequestReview;
use App\Process\Post\ProjectRequestReviewHandler;
use App\Tests\Integration\AbstractAppKernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class RequestReviewProcessTest extends AbstractAppKernelTestCase
{
    public function testRequestReviewForDraft(): void
    {
        $post = $this->anyPost(PostStatus::DRAFT);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $requestReview = new PostRequestReview($post);

        $process = static::getContainer()->get(ProjectRequestReviewHandler::class);
        $process($requestReview);

        self::assertEquals(PostStatus::IN_REVIEW, $post->getStatus());
        self::assertCount($nbChanges + 1, $post->getStatusChanges());
        self::assertEquals(PostStatus::IN_REVIEW, $post->getStatusChanges()->first()->getCurrentStatus());
        self::assertEquals(PostStatus::DRAFT, $post->getStatusChanges()->first()->getPreviousStatus());
    }

    public static function nonDraftPost(): iterable
    {
        return [
            'In review' => [PostStatus::IN_REVIEW],
            'Published' => [PostStatus::PUBLISHED],
            'Archived' => [PostStatus::ARCHIVED],
        ];
    }

    #[DataProvider('nonDraftPost')]
    public function testRequestReviewForNonDraft(PostStatus $status): void
    {
        $post = $this->anyPost($status);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $currentStatus = $post->getStatus();
        $requestReview = new PostRequestReview($post);

        $process = static::getContainer()->get(ProjectRequestReviewHandler::class);
        $process($requestReview);

        self::assertEquals($currentStatus, $post->getStatus());
        self::assertCount($nbChanges, $post->getStatusChanges());
    }


}
