<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Controller\Admin\Post\PostCrudController;
use App\Entity\Enums\PostStatus;
use App\Entity\User;
use App\Story\Factory\PostFactory;
use App\Story\Factory\UserFactory;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use App\Tests\Functional\EasyAdmin\Trait\AdditionalCrudAsserts;
use App\Tests\Story\InitialTestStateStory;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Attribute\WithStory;

#[WithStory(InitialTestStateStory::class)]
class PostDetailTest extends AbstractAppCrudTestCase
{
    use AdditionalCrudAsserts;

    public static function dataForReviewActionDisplay()
    {
        yield "Author own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, true, true];
        yield "Admin own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, true, true];
        yield "Author own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, true, false];
        yield "Admin own in review" => [UserFactory::anyAdmin(), PostStatus::IN_REVIEW, true, false];
        yield "Author own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, true, false];
        yield "Admin own published" => [UserFactory::anyAdmin(), PostStatus::PUBLISHED, true, false];
        yield "Author own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, true, false];
        yield "Admin own archived" => [UserFactory::anyAdmin(), PostStatus::ARCHIVED, true, false];
        yield "Author own rejected" => [UserFactory::anyAuthor(), PostStatus::REJECTED, true, false];
        yield "Admin own rejected" => [UserFactory::anyAdmin(), PostStatus::REJECTED, true, false];
        yield "Author not own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, false, false];
        yield "Publisher not own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, false, false];
        yield "Admin not own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, false, true];
        yield "Author not own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, false, false];
        yield "Publisher not own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, false, false];
        yield "Admin not own in-review" => [UserFactory::anyAdmin(), PostStatus::IN_REVIEW, false, false];
        yield "Author not own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, false, false];
        yield "Publisher not own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, false, false];
        yield "Admin not own published" => [UserFactory::anyAdmin(), PostStatus::PUBLISHED, false, false];
        yield "Author not own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, false, false];
        yield "Publisher not own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, false, false];
        yield "Admin not own archived" => [UserFactory::anyAdmin(), PostStatus::ARCHIVED, false, false];
        yield "Author not own rejected" => [UserFactory::anyAuthor(), PostStatus::REJECTED, false, false];
        yield "Publisher not own rejected" => [UserFactory::anyAuthor(), PostStatus::REJECTED, false, false];
        yield "Admin not own rejected" => [UserFactory::anyAdmin(), PostStatus::REJECTED, false, false];
    }

    #[DataProvider('dataForReviewActionDisplay')]
    public function testRequestReviewActionDisplayed(User $user, PostStatus $status, bool $ownPost, bool $visible): void
    {
        $this->login($user->getEmail());

        $post = match($ownPost) {
            true => PostFactory::anyOwned($user, $status),
            false => PostFactory::anyNotOwned($user, $status),
        };

        $this->client->request(Request::METHOD_GET, $this->generateDetailUrl($post->getId()));
        self::assertResponseIsSuccessful();

        if ($visible) {
            $this->assertPageActionExists('request_review');
        } else {
            $this->assertPageActionNotExists('request_review');
        }
    }

    protected function getControllerFqcn(): string
    {
        return PostCrudController::class;
    }

}
