<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Admin\Action\Post\PostRejectReviewAction;
use App\Admin\Action\Post\PublishPostAction;
use App\Admin\Action\Post\PostRequestReviewAction;
use App\Controller\Admin\Post\PostCrudController;
use App\Entity\Enums\PostStatus;
use App\Entity\Enums\UserRole;
use App\Entity\User;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;
use PHPUnit\Framework\Attributes\DataProvider;

class PostListingTest extends AbstractAppCrudTestCase
{
    use CrudTestIndexAsserts;

    public static function allUsersProvider(): iterable 
    {
        yield "Author" => [UserRole::AUTHOR];
        yield "Admin" => [UserRole::ADMIN];
        yield "Publisher" => [UserRole::PUBLISHER];
    }

    #[DataProvider('allUsersProvider')]
    public function testPostIndexDisplays(UserRole $userRole): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    public static function nonAdminUsersProvider(): iterable
    {
        yield "Author" => [UserRole::AUTHOR];
        yield "Publisher" => [UserRole::PUBLISHER];
    }

    #[DataProvider('nonAdminUsersProvider')]
    public function testPostListingData(UserRole $userRole): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());

        $this->assertIndexColumnExists('title');
        $this->assertIndexColumnExists('status');
        $this->assertIndexColumnExists('author');
        $this->assertIndexColumnExists('statusDate');
        $this->assertIndexColumnExists('createdAt');
    }

    public static function createUsersVisibilityProvider(): iterable
    {
        yield "Author" => [UserRole::AUTHOR, true];
        yield "Publisher" => [UserRole::PUBLISHER, false];
        yield "Admin" => [UserRole::ADMIN, true];
    }

    #[DataProvider('createUsersVisibilityProvider')]
    public function testCreateVisibility(UserRole $userRole, bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        if ($visible) {
            $this->assertGlobalActionExists(Action::NEW);
        } else {
            $this->assertGlobalActionNotExists(Action::NEW);
        }
    }

    public static function detailActionUsersVisibilityProvider(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, true];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, true];
        yield "Author own in_review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, true];
        yield "Admin own in_review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, true];
        yield "Author own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, true];
        yield "Admin own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, true];
        yield "Author own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, true];
        yield "Admin own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, true];

        yield "Author not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, false];
        yield "Publisher not own draft" => [UserRole::PUBLISHER, PostStatus::DRAFT, false];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, false];
        yield "Author not own in_review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, false];
        yield "Publisher not own in_review" => [UserRole::PUBLISHER, PostStatus::IN_REVIEW, false];
        yield "Admin not own in_review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, false];
        yield "Author not own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, false];
        yield "Publisher not own published" => [UserRole::PUBLISHER, PostStatus::PUBLISHED, false];
        yield "Admin not own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, false];
        yield "Author not own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, false];
        yield "Publisher not own archived" => [UserRole::PUBLISHER, PostStatus::ARCHIVED, false];
        yield "Admin not own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, false];
    }

    #[DataProvider('detailActionUsersVisibilityProvider')]
    public function testDetailActionVisibility(UserRole $userRole, PostStatus $status, bool $whichPost): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = match($whichPost) {
            true => $this->anyPostOwned($user, $status),
            false => $this->anyPostNotOwned($user, $status),
        };

        $this->assertIndexEntityActionExists(Action::DETAIL, $post->getId());
    }


    public static function editPostVisibilityProvider(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, 'own', true];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, 'own', true];
        yield "Author own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, 'own', false];
        yield "Admin own in review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, 'own', false];
        yield "Author own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, 'own', false];
        yield "Admin own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, 'own', false];
        yield "Author own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, 'own', false];
        yield "Admin own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, 'own', false];

        yield "Author not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "notOwn", false];
        yield "Publisher not own draft" => [UserRole::PUBLISHER, PostStatus::DRAFT, "notOwn", false];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, "notOwn", true];
        yield "Author not own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Publisher not own in-review" => [UserRole::PUBLISHER, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Admin not own in-review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Author not own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, "notOwn", false];
        yield "Publisher not own published" => [UserRole::PUBLISHER, PostStatus::PUBLISHED, "notOwn", false];
        yield "Admin not own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, "notOwn", false];
        yield "Author not own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, "notOwn", false];
        yield "Publisher not own archived" => [UserRole::PUBLISHER, PostStatus::ARCHIVED, "notOwn", false];
        yield "Admin not own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, "notOwn", false];
    }

    #[DataProvider('editPostVisibilityProvider')]
    public function testEditPostActionVisibility(UserRole $userRole, PostStatus $status, string $whichPost,  bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();
        
        $post = match($whichPost) {
            "own" => $this->anyPostOwned($user, $status),
            'notOwn' => $this->anyPostNotOwned($user, $status),
        };

        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(Action::EDIT, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(Action::EDIT, $post->getId());
        }
    }

    public static function deletePostVisibilityProvider(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "own", true];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, "own", true];
        yield "Author own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, "own", false];
        yield "Admin own in review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, "own", false];
        yield "Author own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, "own", false];
        yield "Admin own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, "own", false];
        yield "Author own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, "own", false];
        yield "Admin own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, "own", false];
        yield "Author not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "notOwn", false];
        yield "Publisher not own draft" => [UserRole::PUBLISHER, PostStatus::DRAFT, "notOwn", false];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, "notOwn", true];
        yield "Author not own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Publisher not own in-review" => [UserRole::PUBLISHER, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Admin not own in-review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Author not own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, "notOwn", false];
        yield "Publisher not own published" => [UserRole::PUBLISHER, PostStatus::PUBLISHED, "notOwn", false];
        yield "Admin not own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, "notOwn", false];
        yield "Author not own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, "notOwn", false];
        yield "Publisher not own archived" => [UserRole::PUBLISHER, PostStatus::ARCHIVED, "notOwn", false];
        yield "Admin not own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, "notOwn", false];
    }

    #[DataProvider('deletePostVisibilityProvider')]
    public function testDeletePostActionVisibility(UserRole $userRole, PostStatus $status, string $whichPost, bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = match($whichPost) {
            "own" => $this->anyPostOwned($user, $status),
            'notOwn' => $this->anyPostNotOwned($user, $status),
        };

        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(Action::DELETE, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(Action::DELETE, $post->getId());
        }
    }

    public static function requestReviewPostVisibilityProvider(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "own", true];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, "own", true];
        yield "Author own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, "own", false];
        yield "Admin own in review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, "own", false];
        yield "Author own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, "own", false];
        yield "Admin own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, "own", false];
        yield "Author own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, "own", false];
        yield "Admin own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, "own", false];

        yield "Author not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "notOwn", false];
        yield "Publisher not own draft" => [UserRole::PUBLISHER, PostStatus::DRAFT, "notOwn", false];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, "notOwn", true];
        yield "Author not own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Publisher not own in-review" => [UserRole::PUBLISHER, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Admin not own in-review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Author not own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, "notOwn", false];
        yield "Publisher not own published" => [UserRole::PUBLISHER, PostStatus::PUBLISHED, "notOwn", false];
        yield "Admin not own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, "notOwn", false];
        yield "Author not own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, "notOwn", false];
        yield "Publisher not own archived" => [UserRole::PUBLISHER, PostStatus::ARCHIVED, "notOwn", false];
        yield "Admin not own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, "notOwn", false];
    }

    #[DataProvider('requestReviewPostVisibilityProvider')]
    public function testRequestReviewPostActionVisibility(UserRole $userRole, PostStatus $status, string $whichPost, bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = match($whichPost) {
            "own" => $this->anyPostOwned($user, $status),
            'notOwn' => $this->anyPostNotOwned($user, $status),
        };
        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(PostRequestReviewAction::NAME, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(PostRequestReviewAction::NAME, $post->getId());
        }
    }

    public static function publishPostVisibilityProvider(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "own", false];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, "own", false];
        yield "Author own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, "own", false];
        yield "Admin own in review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, "own", true];
        yield "Author own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, "own", false];
        yield "Admin own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, "own", false];
        yield "Author own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, "own", false];
        yield "Admin own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, "own", false];

        yield "Author not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "notOwn", false];
        yield "Publisher not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "notOwn", false];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, "notOwn", false];
        yield "Author not own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Publisher not own in-review" => [UserRole::PUBLISHER, PostStatus::IN_REVIEW, "notOwn", true];
        yield "Admin not own in-review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, "notOwn", true];
        yield "Author not own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, "notOwn", false];
        yield "Publisher not own published" => [UserRole::PUBLISHER, PostStatus::PUBLISHED, "notOwn", false];
        yield "Admin not own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, "notOwn", false];
        yield "Author not own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, "notOwn", false];
        yield "Publisher not own archived" => [UserRole::PUBLISHER, PostStatus::ARCHIVED, "notOwn", false];
        yield "Admin not own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, "notOwn", false];
    }

    #[DataProvider('publishPostVisibilityProvider')]
    public function testPublishPostActionVisibility(UserRole $userRole, PostStatus $status, string $whichPost, bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = match($whichPost) {
            "own" => $this->anyPostOwned($user, $status),
            'notOwn' => $this->anyPostNotOwned($user, $status),
        };
        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(PublishPostAction::NAME, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(PublishPostAction::NAME, $post->getId());
        }
    }

    public static function postRejectReviewVisibilityProvider(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "own", false];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, "own", false];
        yield "Author own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, "own", false];
        yield "Admin own in review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, "own", true];
        yield "Author own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, "own", false];
        yield "Admin own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, "own", false];
        yield "Author own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, "own", false];
        yield "Admin own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, "own", false];

        yield "Author not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "notOwn", false];
        yield "Publisher not own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, "notOwn", false];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, "notOwn", false];
        yield "Author not own in-review" => [UserRole::AUTHOR, PostStatus::IN_REVIEW, "notOwn", false];
        yield "Publisher not own in-review" => [UserRole::PUBLISHER, PostStatus::IN_REVIEW, "notOwn", true];
        yield "Admin not own in-review" => [UserRole::ADMIN, PostStatus::IN_REVIEW, "notOwn", true];
        yield "Author not own published" => [UserRole::AUTHOR, PostStatus::PUBLISHED, "notOwn", false];
        yield "Publisher not own published" => [UserRole::PUBLISHER, PostStatus::PUBLISHED, "notOwn", false];
        yield "Admin not own published" => [UserRole::ADMIN, PostStatus::PUBLISHED, "notOwn", false];
        yield "Author not own archived" => [UserRole::AUTHOR, PostStatus::ARCHIVED, "notOwn", false];
        yield "Publisher not own archived" => [UserRole::PUBLISHER, PostStatus::ARCHIVED, "notOwn", false];
        yield "Admin not own archived" => [UserRole::ADMIN, PostStatus::ARCHIVED, "notOwn", false];
    }

    #[DataProvider('postRejectReviewVisibilityProvider')]
    public function testPostRejectReviewActionVisibility(UserRole $userRole, PostStatus $status, string $whichPost, bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = match($whichPost) {
            "own" => $this->anyPostOwned($user, $status),
            'notOwn' => $this->anyPostNotOwned($user, $status),
        };
        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(PostRejectReviewAction::NAME, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(PostRejectReviewAction::NAME, $post->getId());
        }
    }

    protected function getControllerFqcn(): string
    {
        return PostCrudController::class;
    }
}
