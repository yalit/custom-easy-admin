<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Post;

use App\Controller\EasyAdmin\PostCrudController;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserRoles;
use App\Tests\EasyAdmin\Traits\DatabaseReloadTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminPostTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminUserDataTrait;
use App\Tests\EasyAdmin\BaseEasyAdminWebTestCase;
use App\Workflow\PostWorkflow;
use DOMElement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotNull;

class PostCRUDEditTest extends BaseEasyAdminWebTestCase
{
    use EasyAdminUserDataTrait;
    use EasyAdminPostTrait;

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function editOK(User $user)
    {
        $this->loginUser($user);
        $post = $this->getPostWithStatus(PostWorkflow::STATUS_DRAFT);

         $this->getAdminUrl(PostCrudController::class, Action::EDIT, (string)$post->getId());

         self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function editNoGenericEasyAdminTemplateForComments(User $user)
    {
        $this->loginUser($user);
        $post = $this->getPostWithStatus(PostWorkflow::STATUS_DRAFT);

        $this->getAdminUrl(PostCrudController::class, Action::EDIT, (string)$post->getId());

        $collectionItems = $this->client->getCrawler()->filter(".ea-form-collection-items");
        self::assertCount(0, $collectionItems);
    }

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function editDisplaySpecificTemplateForComments(User $user)
    {
        $this->loginUser($user);
        $post = $this->getPostWithStatus(PostWorkflow::STATUS_DRAFT);

        $this->getAdminUrl(PostCrudController::class, Action::EDIT, (string)$post->getId());

        $collectionComments = $this->client->getCrawler()->filter(".custom_collection");
        self::assertCount(1, $collectionComments);

        $collectionItems = $this->client->getCrawler()->filter(".custom_collection_row");
        self::assertCount(6, $collectionItems);

        $collectionTextArea = $this->client->getCrawler()->filter(".custom_collection_row textarea");
        self::assertCount(5, $collectionTextArea);

        /** @var DOMElement $textArea */
        foreach ($collectionTextArea as $textArea) {
            self::assertNotNull($textArea->nodeValue);
        }
    }

    /**
     * @test
     * @dataProvider getAllAuthorUsers
     */
    public function editDisplayRequestReviewActionForAuthorAndAdminForOwnDraftPosts(User $user)
    {
        $this->loginUser($user);
        $post = $this->getPostWithStatusAndAuthor(PostWorkflow::STATUS_DRAFT, $user);

        $this->getAdminUrl(PostCrudController::class, Action::EDIT, (string)$post->getId());

        self::assertSelectorExists("a.action-post_request_review");
    }

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function editDisplayRequestReviewActionForAdminForAnyDraftPosts(User $user)
    {
        $this->loginUser($user);
        $post = $this->getPostWithStatus(PostWorkflow::STATUS_DRAFT);

        $this->getAdminUrl(PostCrudController::class, Action::EDIT, (string)$post->getId());

        self::assertSelectorExists("a.action-post_request_review");
    }

    /**
     * @test
     * @dataProvider getOnlyAuthorUsers
     */
    public function editNotDisplayRequestReviewActionForAuthorForNotOwnDraftPosts(User $user)
    {
        $this->loginUser($user);
        $post = $this->getPostWithStatusAndNotAuthor(PostWorkflow::STATUS_DRAFT, $user);

        $this->getAdminUrl(PostCrudController::class, Action::EDIT, (string)$post->getId());

        self::assertSelectorNotExists("a.action-post_request_review");
    }
}
