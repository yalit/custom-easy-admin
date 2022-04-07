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

    /**************
     * Publish Action
     */

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

    /**************
     * Cancel Action
     */

    /**
     * @test
     * @dataProvider getAllPublisherUsers
     */
    public function cancelActionIsDisplayedOnInReviewPostForPublisher(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        /** @var Post $inReviewPost */
        $inReviewPost = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['status' => PostWorkflow::STATUS_IN_REVIEW]);

        self::assertSelectorExists(
            sprintf('tr[data-id="%d"] .actions .action-post_cancel', $inReviewPost->getId())
        );
    }

    /**
     * @test
     * @dataProvider getAllEasyAdminGrantedNonPublisherUsers
     */
    public function cancelActionIsNotDisplayedOnInReviewPostForNonPublisher(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        /** @var Post $inReviewPost */
        $inReviewPost = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['status' => PostWorkflow::STATUS_IN_REVIEW]);

        self::assertSelectorNotExists(
            sprintf('tr[data-id="%d"] .actions .action-post_cancel', $inReviewPost->getId())
        );
    }

    /**
     * @test
     * @dataProvider getAllPublisherUsers
     */
    public function cancelActionNeedConfirmation(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        /** @var Post $inReviewPost */
        $inReviewPost = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['status' => PostWorkflow::STATUS_IN_REVIEW]);

        $this->clickOnElementRowAction($inReviewPost->getId(), 'post_cancel');
        $this->client->waitForVisibility("#confirmation-modal");

        self::assertSelectorIsVisible("#btn-confirm");
        self::assertSelectorIsVisible("#btn-cancel");
    }


}
