<?php

namespace App\Tests\Integration\Process\Post;

use App\Entity\Enums\PostStatus;
use App\Entity\Post;
use App\Process\Post\PostRequestReview;
use App\Process\Post\ProjectRequestReviewHandler;
use App\Story\Factory\PostFactory;
use App\Tests\Integration\AbstractAppKernelTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class RequestReviewProcessTest extends AbstractAppKernelTestCase
{
    public static function nonDraftPost(): iterable
    {
        return [
            'In review' => [PostFactory::manyDraft(1)],
            'Published' => [PostFactory::manyDraft(1)],
            'Archived' => [PostFactory::manyDraft(1)],
            'Rejected' => [PostFactory::manyDraft(1)],
        ];
    }

    public static function draftPost()
    {
        yield [PostFactory::anyPost(1, PostStatus::DRAFT)];
    }

    #[DataProvider('draftPost')]
    public function testRequestReviewForDraft(Post $post): void
    {
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

    #[DataProvider('nonDraftPost')]
    public function testRequestReviewForNonDraft(Post $post): void
    {
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
