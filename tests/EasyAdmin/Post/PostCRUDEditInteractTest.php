<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Post;

use App\Controller\EasyAdmin\PostCrudController;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Tests\EasyAdmin\BaseEasyAdminPantherTestCase;
use App\Tests\EasyAdmin\Traits\DatabaseReloadTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminActionTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminPostTrait;
use App\Tests\EasyAdmin\Traits\EasyAdminUserDataTrait;
use App\Tests\EasyAdmin\BaseEasyAdminWebTestCase;
use App\Workflow\PostWorkflow;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class PostCRUDEditInteractTest extends BaseEasyAdminPantherTestCase
{
    use EasyAdminUserDataTrait;
    use EasyAdminActionTrait;
    use EasyAdminPostTrait;

    private const actionSaveAndReturnButtonFilter = ".action-saveAndReturn";
    private const addButtonCssCriteria = ".custom_collection_row .add_row";
    private const textAreaId = "#Post_comments_%d_content";

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function editAddAComment(User $user)
    {
        $this->loginUser($user);
        $post  = $this->getPostWithStatus(PostWorkflow::STATUS_DRAFT);

        $this->clickOnElementRowAction($post->getId(), Action::EDIT);

        $this->client->refreshCrawler();
        $originalTextAreas = $this->client->getCrawler()->filter(".custom_collection_row textarea");

        $addButton = $this->client->getCrawler()->filter(self::addButtonCssCriteria);
        $addButton->getLocationOnScreenOnceScrolledIntoView();
        $addButton->click();
        $newTextAreaCssFilter = sprintf(self::textAreaId, $originalTextAreas->count());
        $this->client->waitForVisibility($newTextAreaCssFilter);
        self::assertSelectorTextSame($newTextAreaCssFilter, "");

        $newTextForNewComment = "Additional Comment for test";
        $this->client->getCrawler()->filter($newTextAreaCssFilter)->sendKeys($newTextForNewComment);

        $this->clickOnSaveButton();

        $this->entityManager->refresh($post);
        self::assertCount($originalTextAreas->count() + 1, $post->getComments());
    }

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function editEditAComment(User $user)
    {
        $this->loginUser($user);
        $post  = $this->getPostWithStatus(PostWorkflow::STATUS_DRAFT);

        $this->clickOnElementRowAction($post->getId(), Action::EDIT);

        $this->client->refreshCrawler();

        $textarea = $this->client->getCrawler()->filter(sprintf(self::textAreaId, 0))->first();

        $textForUpdatedComment = "Additional Comment for test";
        $textarea->sendKeys($textForUpdatedComment);

        $this->clickOnSaveButton();
        $this->entityManager->refresh($post);

        /** @var Comment $comment */
        $comment = $post->getComments()->first();
        self::assertStringEndsWith($textForUpdatedComment, $comment->getContent());
    }

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function editDeleteAComment(User $user)
    {
        $this->loginUser($user);
        $post  = $this->getPostWithStatus(PostWorkflow::STATUS_DRAFT);

        $this->clickOnElementRowAction($post->getId(), Action::EDIT);

        $this->client->refreshCrawler();

        $firstCollectionRow = $this->client->getCrawler()->filter(".custom_collection_row")->first();
        $removeButton = $firstCollectionRow->filter(".remove_row");
        $removeButton->getLocationOnScreenOnceScrolledIntoView();

        $removeButton->click();

        self::assertSelectorNotExists(sprintf(self::textAreaId, 0));

        $this->clickOnSaveButton();

        $this->entityManager->refresh($post);
        self::assertCount(4, $post->getComments());
    }

    private function clickOnSaveButton()
    {
        $this->client->getCrawler()->filter(self::actionSaveAndReturnButtonFilter)->click();
    }
}
