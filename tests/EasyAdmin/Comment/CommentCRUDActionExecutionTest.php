<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Comment;

use App\Entity\Comment;
use App\Entity\User;
use App\Tests\EasyAdmin\BaseEasyAdminPantherTestCase;
use App\Tests\EasyAdmin\Traits\EasyAdminActionTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminRoutingTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminUserDataTrait;
use App\Workflow\CommentWorkflow;
use Facebook\WebDriver\Remote\RemoteWebElement;

class CommentCRUDActionExecutionTest extends BaseEasyAdminPantherTestCase
{
    use EasyAdminUserDataTrait;
    use EasyAdminRoutingTrait;
    use EasyAdminActionTrait;

    /**
     * @test
     * @dataProvider getAllReviewerUsers
     */
    public function publishActionIsExecutedAfterConfirmation(User $user): void
    {
        $this->loginUser($user);

        $this->goToCommentIndex();

        $createdCommentRows = $this->client
            ->getCrawler()
            ->filter(sprintf('tbody tr'));

        /** @var RemoteWebElement $commentRow */
        foreach ($createdCommentRows as $commentRow) {
            $commentId = $commentRow->getAttribute('data-id');

            $createdComment = $this->entityManager->getRepository(Comment::class)->find($commentId);

            $commentRowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%s"] span[title="%s"]', $commentId, CommentWorkflow::STATUS_CREATED));
            if (count($commentRowStatus) === 0) {
                continue;
            }

            $this->clickOnElementRowAction((int)$commentId, 'comment_publish');
            $this->client->waitForVisibility("#confirmation-modal");

            $btnConfirm = $this->client->getCrawler()->filter('#btn-confirm');
            $btnConfirm->click();

            $this->client->waitForInvisibility('#confirmation-modal');
            $this->entityManager->refresh($createdComment);
            static::assertEquals(CommentWorkflow::STATUS_PUBLISHED, $createdComment->getStatus());

            break;
        }
    }

    /**
     * @test
     * @dataProvider getAllReviewerUsers
     */
    public function commentActionIsExecutedAfterConfirmation(User $user): void
    {
        $this->loginUser($user);

        $this->goToCommentIndex();

        $createdCommentRows = $this->client
            ->getCrawler()
            ->filter(sprintf('tbody tr'));

        /** @var RemoteWebElement $commentRow */
        foreach ($createdCommentRows as $commentRow) {
            $commentId = $commentRow->getAttribute('data-id');

            $createdComment = $this->entityManager->getRepository(Comment::class)->find($commentId);

            $commentRowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%s"] span[title="%s"]', $commentId, CommentWorkflow::STATUS_CREATED));
            if (count($commentRowStatus) === 0) {
                continue;
            }

            $this->clickOnElementRowAction((int)$commentId, 'comment_cancel');
            $this->client->waitForVisibility("#confirmation-modal");

            $btnConfirm = $this->client->getCrawler()->filter('#btn-confirm');
            $btnConfirm->click();

            $this->client->waitForInvisibility('#confirmation-modal');
            $this->entityManager->refresh($createdComment);
            static::assertEquals(CommentWorkflow::STATUS_CANCELLED, $createdComment->getStatus());

            break;
        }
    }
}
