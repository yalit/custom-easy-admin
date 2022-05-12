<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Comment;

use App\Controller\EasyAdmin\CommentCrudController;
use App\Entity\Comment;
use App\Entity\User;
use App\Tests\EasyAdmin\Traits\EasyAdminUserDataTrait;
use App\Tests\EasyAdmin\BaseEasyAdminWebTestCase;
use App\Workflow\CommentWorkflow;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class CommentCRUDIndexTest extends BaseEasyAdminWebTestCase
{
    use EasyAdminUserDataTrait;

    /**
     * @test
     * @dataProvider getAllEasyAdminUsers
     */
    public function indexDisplaysCorrectCommentStatuses(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(CommentCrudController::class, Action::INDEX);

        $commentRows = $this->client->getCrawler()->filter("tbody tr");

        foreach ($commentRows as $commentRow) {
            $commentId = ($commentRow->attributes['data-id']->value);

            $rowStatus = $this->client->getCrawler()->filter(sprintf('tr[data-id="%d"] td[data-label="Status"]', $commentId));
            self::assertCount(1, $rowStatus);

            $comment = $this->entityManager->getRepository(Comment::class)->find($commentId);

            self::assertNotNull($comment);
            self::assertEquals($rowStatus->filter("span")->getNode(0)->attributes['title']->value, $comment->getStatus());
            self::assertContains($rowStatus->text(), $this->getCommentStatuses());
        }
    }

    /**
     * @return Array<string, string>
     */
    private function getCommentStatuses(): array
    {
        return [
            CommentWorkflow::STATUS_CREATED => "Created",
            CommentWorkflow::STATUS_PUBLISHED => "Published",
            CommentWorkflow::STATUS_CANCELLED => "Cancelled",
        ];
    }
}
