<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserRoles;
use App\Tests\EasyAdmin\BaseEasyAdminPantherTestCase;
use App\Tests\EasyAdmin\Traits\EasyAdminActionTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminRoutingTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminUserDataTrait;
use App\Workflow\PostWorkflow;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\DomCrawler\Crawler;

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

    /**************
     * Request review Action
     */

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function requestReviewActionIsDisplayedOnDraftPostForAdmin(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        $rows = $this->client->getCrawler()->filter('tbody tr');

        /** @var RemoteWebElement $row */
        foreach ($rows as $row) {
            $status = $row->findElement(WebDriverBy::cssSelector('td[data-label="Status"]'))->getText();
            $id = $row->findElement(WebDriverBy::cssSelector('td[data-label="ID"]'))->getText();
            if ($status === "Draft") {
                self::assertSelectorExists(
                    sprintf('tr[data-id="%s"] .actions .action-post_request_review', $id)
                );
            }
        }
    }

    /**
     * @test
     * @dataProvider getOnlyAuthorUsers
     */
    public function requestReviewActionIsDisplayedOnDraftPostForPostAuthor(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        $user = $this->getActualUserFromUser($user);

        $rows = $this->client->getCrawler()->filter('tbody tr');

        /** @var RemoteWebElement $row */
        foreach ($rows as $row) {
            $status = $row->findElement(WebDriverBy::cssSelector('td[data-label="Status"]'))->getText();
            $id = $row->findElement(WebDriverBy::cssSelector('td[data-label="ID"]'))->getText();
            if ($status === "Draft") {
                $post = $this->entityManager->getRepository(Post::class)->findOneBy(['id' => $id, 'author' => $user]);
                if ($post === null) {
                    self::assertSelectorNotExists(
                        sprintf('tr[data-id="%s"] .actions .action-post_request_review', $id)
                    );
                } else {
                    self::assertSelectorExists(
                        sprintf('tr[data-id="%s"] .actions .action-post_request_review', $id)
                    );
                }

            }
        }
    }

    /**
     * @test
     * @dataProvider getOnlyPublisherUsers
     */
    public function requestReviewActionIsNotDisplayedOndraftPostForNonAuthors(User $user): void
    {
        $this->loginUser($user);

        $this->goToPostIndex();

        $rows = $this->client->getCrawler()->filter('tbody tr');

        /** @var RemoteWebElement $row */
        foreach ($rows as $row) {
            $status = $row->findElement(WebDriverBy::cssSelector('td[data-label="Status"]'))->getText();
            $id = $row->findElement(WebDriverBy::cssSelector('td[data-label="ID"]'))->getText();
            if ($status === "Draft") {
                self::assertSelectorNotExists(
                    sprintf('tr[data-id="%s"] .actions .action-post_request_review', $id)
                );
            }
        }
    }

    /**
     * @test
     * @dataProvider getAllAuthorUsers
     */
    public function requestReviewActionNeedConfirmation(User $user): void
    {
        $this->loginUser($user);

        $user = $this->getActualUserFromUser($user);

        $this->goToPostIndex();

        $rows = $this->client->getCrawler()->filter('tbody tr');

        /** @var RemoteWebElement $row */
        foreach ($rows as $row) {
            $status = $row->findElement(WebDriverBy::cssSelector('td[data-label="Status"]'))->getText();
            $id = $row->findElement(WebDriverBy::cssSelector('td[data-label="ID"]'))->getText();
            if ($status === "Draft") {
                $post = $this->entityManager->getRepository(Post::class)->findOneBy(['id' => $id, 'author' => $user]);
                if ($post === null) {
                    continue;
                } else {
                    $this->clickOnElementRowAction($post->getId(), 'post_request_review');
                    $this->client->waitForVisibility("#confirmation-modal");

                    self::assertSelectorIsVisible("#btn-confirm");
                    self::assertSelectorIsVisible("#btn-cancel");
                    $this->client->getCrawler()->filter("#btn-cancel")->click();
                    $this->client->waitForInvisibility("#confirmation-modal");
                }
                $this->client->refreshCrawler();
            }
        }
    }

    /***********
     * Create
     */

    /**
     * @test
     * @dataProvider getAllAuthorUsers
     */
    public function authorsCanCreatePosts(User $user): void
    {
        $this->loginUser($user);
        $this->goToPostIndex();

        $createButton = $this->client->getCrawler()->filter('a.action-new');
        self::assertCount(1, $createButton);
    }

    /**
     * @test
     * @dataProvider getEasyAdminAllNonAuthorUsers
     */
    public function nonAuthorsCannotCreatePosts(User $user): void
    {
        $this->loginUser($user);
        $this->goToPostIndex();

        $createButton = $this->client->getCrawler()->filter('a.action-new');
        self::assertCount(0, $createButton);
    }
}
