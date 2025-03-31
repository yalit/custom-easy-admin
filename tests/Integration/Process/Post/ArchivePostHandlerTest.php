<?php

namespace App\Tests\Integration\Process\Post;

use App\Entity\Enums\PostStatus;
use App\Process\Post\ArchivePost;
use App\Process\Post\ArchivePostHandler;
use App\Process\Post\PublishPost;
use App\Process\Post\PublishPostHandler;
use App\Tests\Integration\AbstractAppKernelTestCase;
use App\Tests\Story\InitialTestStateStory;
use PHPUnit\Framework\Attributes\DataProvider;

class ArchivePostHandlerTest extends AbstractAppKernelTestCase
{
    public function testArchivePostForPublished(): void
    {
        $post = $this->anyPost(PostStatus::PUBLISHED);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $archivePost = new ArchivePost($post);

        $process = static::getContainer()->get(ArchivePostHandler::class);
        $process($archivePost);

        self::assertEquals(PostStatus::ARCHIVED, $post->getStatus());
        self::assertCount($nbChanges + 1, $post->getStatusChanges());
        self::assertEquals(PostStatus::ARCHIVED, $post->getStatusChanges()->first()->getCurrentStatus());
        self::assertEquals(PostStatus::PUBLISHED, $post->getStatusChanges()->first()->getPreviousStatus());
    }

    public static function nonInReviewPostStatus(): iterable
    {
        return [
            'Draft' => [PostStatus::DRAFT],
            'In-Review' => [PostStatus::IN_REVIEW],
            'Archived' => [PostStatus::ARCHIVED],
        ];
    }

    #[DataProvider('nonInReviewPostStatus')]
    public function testArchivePostForNotPublished(PostStatus $status): void
    {
        $post = $this->anyPost($status);
        $allChanges = $post->getStatusChanges();
        $nbChanges = count($allChanges);
        $currentStatus = $post->getStatus();
        $archivePost = new ArchivePost($post);

        $process = static::getContainer()->get(ArchivePostHandler::class);
        $process($archivePost);

        self::assertEquals($currentStatus, $post->getStatus());
        self::assertCount($nbChanges, $post->getStatusChanges());
    }
}
