<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Tests\EasyAdmin\BaseEasyAdminPantherTestCase;
use App\Tests\EasyAdmin\Traits\EasyAdminActionTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminRoutingTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminUserDataTrait;
use App\Workflow\PostWorkflow;

class PostCRUDActionIndexTest extends BaseEasyAdminPantherTestCase
{
    use EasyAdminUserDataTrait;
    use EasyAdminRoutingTrait;
    use EasyAdminActionTrait;

    /**
     * @test
     * @dataProvider getAllEasyAdminUsers
     */
    public function indexOKWithPanther(User $user): void
    {
        $this->loginUser($user);

        self::assertSelectorIsVisible('h1.title');
        self::assertSelectorTextContains('h1.title', 'Post');
    }

    /**
     * @test
     * @dataProvider getAllEasyAdminGrantedNonPublisherUsers
     */
    public function publishActionIsNotDisplayedOnInReviewPostForNonPublisher(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        /** @var Post $inReviewPost */
        $inReviewPost = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['status' => PostWorkflow::STATUS_IN_REVIEW]);

        self::assertSelectorNotExists(
            sprintf('tr[data-id="%d"] .actions .action-post_publish', $inReviewPost->getId())
        );
    }

    /**
     * @test
     * @dataProvider getAllPublisherUsers
     */
    public function publishActionIsDisplayedOnInReviewPostForPublisher(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        /** @var Post $inReviewPost */
        $inReviewPost = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['status' => PostWorkflow::STATUS_IN_REVIEW]);

        self::assertSelectorExists(
            sprintf('tr[data-id="%d"] .actions .action-post_publish', $inReviewPost->getId())
        );
    }

    /**
     * @test
     * @dataProvider getAllPublisherUsers
     */
    public function publishActionNeedConfirmation(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        /** @var Post $inReviewPost */
        $inReviewPost = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['status' => PostWorkflow::STATUS_IN_REVIEW]);

        $this->clickOnElementRowAction($inReviewPost->getId(), 'post_publish');
        $this->client->waitForVisibility("#confirmation-modal");

        self::assertSelectorIsVisible("#btn-confirm");
        self::assertSelectorIsVisible("#btn-cancel");
    }
}
