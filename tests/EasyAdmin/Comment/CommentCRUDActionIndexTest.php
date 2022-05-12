<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Comment;

use App\Entity\Post;
use App\Entity\User;
use App\Tests\EasyAdmin\BaseEasyAdminPantherTestCase;
use App\Tests\EasyAdmin\Traits\EasyAdminActionTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminRoutingTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminUserDataTrait;
use App\Workflow\CommentWorkflow;
use App\Workflow\PostWorkflow;
use Facebook\WebDriver\Remote\RemoteWebElement;

class CommentCRUDActionIndexTest extends BaseEasyAdminPantherTestCase
{
    use EasyAdminUserDataTrait;
    use EasyAdminRoutingTrait;
    use EasyAdminActionTrait;

    /**************
     * Publish Action
     */

    /**
     * @test
     * @dataProvider getAllReviewerUsers
     */
    public function publishActionIsDisplayedOnCreatedCommentForReviewers(User $user): void
    {
        $this->loginUser($user);

        $this->goToCommentIndex();

        $createdCommentRows = $this->client
            ->getCrawler()
            ->filter(sprintf('tbody tr'));

        /** @var RemoteWebElement $commentRow */
        foreach ($createdCommentRows as $commentRow) {
            $commentId = $commentRow->getAttribute('data-id');

            $commentRowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%s"] span[title="%s"]', $commentId, CommentWorkflow::STATUS_CREATED));
            if (count($commentRowStatus) === 0) {
                continue;
            }

            self::assertSelectorExists(
                sprintf('tr[data-id="%s"] .actions .action-comment_publish',$commentId)
            );
        }
    }

    /**
     * @test
     * @dataProvider getAllReviewerUsers
     */
    public function cancelActionIsDisplayedOnCreatedCommentForReviewers(User $user): void
    {
        $this->loginUser($user);

        $this->goToCommentIndex();

        $createdCommentRows = $this->client
            ->getCrawler()
            ->filter(sprintf('tbody tr'));

        /** @var RemoteWebElement $commentRow */
        foreach ($createdCommentRows as $commentRow) {
            $commentId = $commentRow->getAttribute('data-id');

            $commentRowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%s"] span[title="%s"]', $commentId, CommentWorkflow::STATUS_CREATED));
            if (count($commentRowStatus) === 0) {
                continue;
            }

            self::assertSelectorExists(
                sprintf('tr[data-id="%s"] .actions .action-comment_cancel',$commentId)
            );
        }
    }

    /**
     * @test
     * @dataProvider getAllEasyAdminGrantedNonReviewerUsers
     */
    public function publishActionIsNotDisplayedOnCommentForNonReviewer(User $user): void
    {
        $this->loginUser($user);

        $this->goToCommentIndex();

        $createdCommentRows = $this->client
            ->getCrawler()
            ->filter(sprintf('tbody tr'));

        /** @var RemoteWebElement $commentRow */
        foreach ($createdCommentRows as $commentRow) {
            $commentId = $commentRow->getAttribute('data-id');

            $commentRowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%s"] span[title="%s"]', $commentId, CommentWorkflow::STATUS_PUBLISHED));
            if (count($commentRowStatus) === 0) {
                continue;
            }

            self::assertSelectorNotExists(
                sprintf('tr[data-id="%d"] .actions .action-comment_publish', $commentId)
            );
        }

        /** @var RemoteWebElement $commentRow */
        foreach ($createdCommentRows as $commentRow) {
            $commentId = $commentRow->getAttribute('data-id');

            $commentRowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%s"] span[title="%s"]', $commentId, CommentWorkflow::STATUS_CANCELLED));
            if (count($commentRowStatus) === 0) {
                continue;
            }

            self::assertSelectorNotExists(
                sprintf('tr[data-id="%d"] .actions .action-comment_publish', $commentId)
            );
        }
    }

    /**
     * @test
     * @dataProvider getAllEasyAdminGrantedNonReviewerUsers
     */
    public function cancelActionIsNotDisplayedOnCommentForNonReviewer(User $user): void
    {
        $this->loginUser($user);

        $this->goToCommentIndex();

        $createdCommentRows = $this->client
            ->getCrawler()
            ->filter(sprintf('tbody tr'));

        /** @var RemoteWebElement $commentRow */
        foreach ($createdCommentRows as $commentRow) {
            $commentId = $commentRow->getAttribute('data-id');

            $commentRowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%s"] span[title="%s"]', $commentId, CommentWorkflow::STATUS_CREATED));
            if (count($commentRowStatus) === 0) {
                continue;
            }


            self::assertSelectorNotExists(
                sprintf('tr[data-id="%d"] .actions .action-comment_cancel', $commentId),
                sprintf("There is a Cancel Comment action for the comment %d for user %s", $commentId, $user->getUsername())
            );
        }
    }

    /**
     * @test
     * @dataProvider getAllReviewerUsers
     */
    public function publishActionNeedConfirmation(User $user): void
    {
        $this->loginUser($user);

        $this->goToCommentIndex();

        $createdCommentRows = $this->client
            ->getCrawler()
            ->filter(sprintf('tbody tr'));

        /** @var RemoteWebElement $commentRow */
        foreach ($createdCommentRows as $commentRow) {
            $commentId = $commentRow->getAttribute('data-id');

            $commentRowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%s"] span[title="%s"]', $commentId, CommentWorkflow::STATUS_CREATED));
            if (count($commentRowStatus) === 0) {
                continue;
            }

            $this->clickOnElementRowAction((int)$commentId, 'comment_publish');
            $this->client->waitForVisibility("#confirmation-modal");

            self::assertSelectorIsVisible("#btn-confirm");
            self::assertSelectorIsVisible("#btn-cancel");
            $this->client->getCrawler()->filter("#btn-cancel")->click();
            $this->client->waitForInvisibility("#confirmation-modal");
        }
    }

    /**
     * @test
     * @dataProvider getAllReviewerUsers
     */
    public function cancelActionNeedConfirmation(User $user): void
    {
        $this->loginUser($user);

        $this->goToCommentIndex();

        $createdCommentRows = $this->client
            ->getCrawler()
            ->filter(sprintf('tbody tr'));

        /** @var RemoteWebElement $commentRow */
        foreach ($createdCommentRows as $commentRow) {
            $commentId = $commentRow->getAttribute('data-id');

            $commentRowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%s"] span[title="%s"]', $commentId, CommentWorkflow::STATUS_CREATED));
            if (count($commentRowStatus) === 0) {
                continue;
            }

            $this->clickOnElementRowAction((int)$commentId, 'comment_cancel');
            $this->client->waitForVisibility("#confirmation-modal");

            self::assertSelectorIsVisible("#btn-confirm");
            self::assertSelectorIsVisible("#btn-cancel");
            $this->client->getCrawler()->filter("#btn-cancel")->click();
            $this->client->waitForInvisibility("#confirmation-modal");
        }
    }
}
