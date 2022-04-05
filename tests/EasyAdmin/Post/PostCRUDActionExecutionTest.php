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

class PostCRUDActionExecutionTest extends BaseEasyAdminPantherTestCase
{
    use EasyAdminUserDataTrait;
    use EasyAdminRoutingTrait;
    use EasyAdminActionTrait;

    /**
     * @test
     * @dataProvider getAllPublisherUsers
     */
    public function publishActionIsExecutedAfterConfirmation(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        /** @var Post $inReviewPost */
        $inReviewPost = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['status' => PostWorkflow::STATUS_IN_REVIEW]);

        $this->clickOnElementRowAction($inReviewPost->getId(), 'post_publish');
        $this->client->waitForVisibility("#confirmation-modal");

        $btnConfirm = $this->client->getCrawler()->filter('#btn-confirm');
        $btnConfirm->click();

        $this->client->waitForInvisibility('#confirmation-modal');
        $this->entityManager->refresh($inReviewPost);
        static::assertEquals(PostWorkflow::STATUS_PUBLISHED, $inReviewPost->getStatus());
    }
}
