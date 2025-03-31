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

class PublishPostHandlerTest extends AbstractAppKernelTestCase
{
    public function testPublishPostForInReview(): void
    {
        $post = $this->anyPost(PostStatus::IN_REVIEW);
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

    public static function nonInReviewPostStatus(): iterable
    {
        return [
            'Draft' => [PostStatus::DRAFT],
            'Published' => [PostStatus::PUBLISHED],
            'Archived' => [PostStatus::ARCHIVED],
        ];
    }

    #[DataProvider('nonInReviewPostStatus')]
    public function testPublishPostForNotInReview(PostStatus $status): void
    {
        $post = $this->anyPost($status);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $currentStatus = $post->getStatus();
        $requestReview = new PublishPost($post);

        $process = static::getContainer()->get(PublishPostHandler::class);
        $process($requestReview);

        self::assertEquals($currentStatus, $post->getStatus());
        self::assertCount($nbChanges, $post->getStatusChanges());
    }
}
