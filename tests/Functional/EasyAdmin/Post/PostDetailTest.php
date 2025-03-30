<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Admin\Action\Post\PostRejectReviewAction;
use App\Admin\Action\Post\PostRequestReviewAction;
use App\Admin\Action\Post\PublishPostAction;
use App\Controller\Admin\Post\PostCrudController;
use App\Entity\Enums\PostStatus;
use App\Entity\Enums\UserRole;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;

class PostDetailTest extends AbstractAppCrudTestCase
{
    public static function dataForRequestReviewActionDisplay(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, true, true];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, true, true];
        yield "Author own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, true, false];
        yield "Admin own in review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, true, false];
        yield "Author own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, true, false];
        yield "Admin own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, true, false];
        yield "Author own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, true, false];
        yield "Admin own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, true, false];
        yield "Author not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, false, false];
        yield "Publisher not own draft" => [UserRole::PUBLISHER, PostStatus::DRAFT, false, false];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, false, true];
        yield "Author not own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, false, false];
        yield "Publisher not own in-review" => [UserRole::PUBLISHER, PostStatus::IN_REVIEW, false, false];
        yield "Admin not own in-review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, false, false];
        yield "Author not own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, false, false];
        yield "Publisher not own published" => [UserRole::PUBLISHER, PostStatus::PUBLISHED, false, false];
        yield "Admin not own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, false, false];
        yield "Author not own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, false, false];
        yield "Publisher not own archived" => [UserRole::PUBLISHER, PostStatus::ARCHIVED, false, false];
        yield "Admin not own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, false, false];
    }

    #[DataProvider('dataForRequestReviewActionDisplay')]
    public function testRequestReviewActionDisplayed(UserRole $userRole, PostStatus $status, bool $ownPost, bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());

        $post = match($ownPost) {
            true => $this->anyPostOwned($user, $status),
            false => $this->anyPostNotOwned($user, $status),
        };

        $this->client->request(Request::METHOD_GET, $this->generateDetailUrl($post->getId()));
        self::assertResponseIsSuccessful();

        if ($visible) {
            $this->assertPageActionExists(PostRequestReviewAction::NAME);
        } else {
            $this->assertPageActionNotExists(PostRequestReviewAction::NAME);
        }
    }

    public static function dataForPublishActionDisplay(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, true, false];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, true, false];
        yield "Author own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, true, false];
        yield "Admin own in review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, true, true];
        yield "Author own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, true, false];
        yield "Admin own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, true, false];
        yield "Author own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, true, false];
        yield "Admin own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, true, false];
        yield "Author not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, false, false];
        yield "Publisher not own draft" => [UserRole::PUBLISHER, PostStatus::DRAFT, false, false];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, false, false];
        yield "Author not own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, false, false];
        yield "Publisher not own in-review" => [UserRole::PUBLISHER, PostStatus::IN_REVIEW, false, true];
        yield "Admin not own in-review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, false, true];
        yield "Author not own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, false, false];
        yield "Publisher not own published" => [UserRole::PUBLISHER, PostStatus::PUBLISHED, false, false];
        yield "Admin not own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, false, false];
        yield "Author not own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, false, false];
        yield "Publisher not own archived" => [UserRole::PUBLISHER, PostStatus::ARCHIVED, false, false];
        yield "Admin not own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, false, false];
    }

    #[DataProvider('dataForPublishActionDisplay')]
    public function testPublishActionDisplayed(UserRole $userRole, PostStatus $status, bool $ownPost, bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());

        $post = match($ownPost) {
            true => $this->anyPostOwned($user, $status),
            false => $this->anyPostNotOwned($user, $status),
        };

        $this->client->request(Request::METHOD_GET, $this->generateDetailUrl($post->getId()));
        self::assertResponseIsSuccessful();

        if ($visible) {
            $this->assertPageActionExists(PublishPostAction::NAME);
        } else {
            $this->assertPageActionNotExists(PublishPostAction::NAME);
        }
    }


    public static function dataForRejectReviewActionDisplay(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, true, false];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, true, false];
        yield "Author own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, true, false];
        yield "Admin own in review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, true, true];
        yield "Author own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, true, false];
        yield "Admin own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, true, false];
        yield "Author own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, true, false];
        yield "Admin own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, true, false];

        yield "Author not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, false, false];
        yield "Publisher not own draft" => [UserRole::PUBLISHER, PostStatus::DRAFT, false, false];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, false, false];
        yield "Author not own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, false, false];
        yield "Publisher not own in-review" => [UserRole::PUBLISHER, PostStatus::IN_REVIEW, false, true];
        yield "Admin not own in-review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, false, true];
        yield "Author not own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, false, false];
        yield "Publisher not own published" => [UserRole::PUBLISHER, PostStatus::PUBLISHED, false, false];
        yield "Admin not own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, false, false];
        yield "Author not own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, false, false];
        yield "Publisher not own archived" => [UserRole::PUBLISHER, PostStatus::ARCHIVED, false, false];
        yield "Admin not own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, false, false];
    }

    #[DataProvider('dataForRejectReviewActionDisplay')]
    public function testRejectReviewActionDisplayed(UserRole $userRole, PostStatus $status, bool $ownPost, bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());

        $post = match($ownPost) {
            true => $this->anyPostOwned($user, $status),
            false => $this->anyPostNotOwned($user, $status),
        };

        $this->client->request(Request::METHOD_GET, $this->generateDetailUrl($post->getId()));
        self::assertResponseIsSuccessful();

        if ($visible) {
            $this->assertPageActionExists(PostRejectReviewAction::NAME);
        } else {
            $this->assertPageActionNotExists(PostRejectReviewAction::NAME);
        }
    }

    protected function getControllerFqcn(): string
    {
        return PostCrudController::class;
    }

}
