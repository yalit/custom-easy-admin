<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Post;

use App\Controller\EasyAdmin\PostCrudController;
use App\Entity\Post;
use App\Entity\User;
use App\Tests\EasyAdmin\EasyAdminUserDataTrait;
use App\Tests\EasyAdmin\BaseEasyAdminWebTestCase;
use App\Workflow\PostWorkflow;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class PostCRUDIndexTest extends BaseEasyAdminWebTestCase
{
    use EasyAdminUserDataTrait;
    /**
     * 1. status is correctly displayed
     * 2. correct actions existing for specific users
     * 3. dates are correctly displayed
     */

    /**
     * @test
     * @dataProvider getAllEasyAdminUsers
     */
    public function indexDisplaysCorrectPostStatuses(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(PostCrudController::class, Action::INDEX);

        $crawler = $this->client->getCrawler();

        $statuses = $this->getPostStatuses();

        foreach ($statuses as $status => $display) {
            /** @var Post $post */
            $post = $this->entityManager
                ->getRepository(Post::class)
                ->findOneBy(['status' => $status])
            ;
            $fieldStatus = $crawler
                ->filter(sprintf('tr[data-id="%d"] .field-text[data-label="Status"]', $post->getId()))
            ;
            self::assertCount(1, $fieldStatus);

            self::assertEquals($display, $fieldStatus->text());
        }
    }

    /**
     * @test
     * @dataProvider getAllEasyAdminUsers
     */
    public function indexDisplaysCorrectPostStatusDate(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(PostCrudController::class, Action::INDEX);

        $crawler = $this->client->getCrawler();

        $statuses = $this->getPostStatuses();

        foreach ($statuses as $status => $display) {
            /** @var Post $post */
            $post = $this->entityManager
                ->getRepository(Post::class)
                ->findOneBy(['status' => $status])
            ;
            $fieldStatusDate = $crawler
                ->filter(sprintf('tr[data-id="%d"] > .field-datetime[data-label="Status Date"] > time', $post->getId()))
            ;
            self::assertCount(1, $fieldStatusDate);

            switch ($status) {
                case PostWorkflow::STATUS_DRAFT:
                    $expected = $post->getCreatedAt();
                    break;
                case PostWorkflow::STATUS_IN_REVIEW:
                    $expected = $post->getInReviewAt();
                    break;
                case PostWorkflow::STATUS_PUBLISHED:
                    $expected = $post->getPublishedAt();
                    break;
                case PostWorkflow::STATUS_CANCELLED:
                    $expected = $post->getCancelledAt();
                    break;
            }
            $actual = new \DateTimeImmutable($fieldStatusDate->attr('datetime'), $post->getStatusDate()->getTimezone());

            self::assertEquals(
                $expected->getTimestamp(),
                $actual->getTimestamp()
            );
        }
    }

    /**
     * @return Array<string, string>
     */
    private function getPostStatuses(): array
    {
        return [
            PostWorkflow::STATUS_DRAFT => "Draft",
            PostWorkflow::STATUS_IN_REVIEW => "In Review",
            PostWorkflow::STATUS_PUBLISHED => "Published",
            PostWorkflow::STATUS_CANCELLED => "Cancelled",
        ];
    }
}
