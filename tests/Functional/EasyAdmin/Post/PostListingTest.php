<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Controller\Admin\PostCrudController;
use App\Entity\Enums\PostStatus;
use App\Entity\Post;
use App\Entity\User;
use App\Story\Factory\UserFactory;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use App\Tests\Story\FunctionalTestStory;
use App\Tests\Story\InitialTestStateStory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Attribute\WithStory;

#[WithStory(InitialTestStateStory::class)]
class PostListingTest extends AbstractAppCrudTestCase
{
    use CrudTestIndexAsserts;

    public static function allUsersProvider(): array
    {
        return FunctionalTestStory::oneUserOfEach();
    }

    #[DataProvider('allUsersProvider')]
    public function testPostIndexDisplays(User $user): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    public static function nonAdminUsersProvider(): array
    {
        return FunctionalTestStory::noAdminUsers();
    }

    #[DataProvider('nonAdminUsersProvider')]
    public function testPostListingData(User $user): void
    {
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
        yield "Author" => [UserFactory::anyAuthor(), true];
        yield "Publisher" => [UserFactory::anyPublisher(), false];
        yield "Admin" => [UserFactory::anyAdmin(), true];
    }

    #[DataProvider('createUsersVisibilityProvider')]
    public function testCreateVisibility(User $user, bool $visible): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        if ($visible) {
            $this->assertGlobalActionExists(Action::NEW);
        } else {
            $this->assertGlobalActionNotExists(Action::NEW);
        }
    }

    public static function editPostVisibilityProvider(): iterable
    {
        yield "Author own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, 'own', true];
        yield "Admin own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, 'own', true];
        yield "Author own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, 'own', false];
        yield "Admin own in review" => [UserFactory::anyAdmin(), PostStatus::IN_REVIEW, 'own', false];
        yield "Author own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, 'own', false];
        yield "Admin own published" => [UserFactory::anyAdmin(), PostStatus::PUBLISHED, 'own', false];
        yield "Author own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, 'own', false];
        yield "Admin own archived" => [UserFactory::anyAdmin(), PostStatus::ARCHIVED, 'own', false];
        yield "Author not own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, "notOwn", false];
        yield "Publisher not own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, "notOwn", false];
        yield "Admin not own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, "notOwn", true];
        yield "Author not own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, "notOwn", false];
        yield "Publisher not own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, "notOwn", false];
        yield "Admin not own in-review" => [UserFactory::anyAdmin(), PostStatus::IN_REVIEW, "notOwn", false];
        yield "Author not own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, "notOwn", false];
        yield "Publisher not own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, "notOwn", false];
        yield "Admin not own published" => [UserFactory::anyAdmin(), PostStatus::PUBLISHED, "notOwn", false];
        yield "Author not own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, "notOwn", false];
        yield "Publisher not own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, "notOwn", false];
        yield "Admin not own archived" => [UserFactory::anyAdmin(), PostStatus::ARCHIVED, "notOwn", false];
    }

    #[DataProvider('editPostVisibilityProvider')]
    public function testEditPostActionVisibility(User $user, PostStatus $status, string $whichPost,  bool $visible): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();
        
        $post = match($whichPost) {
            "own" => $this->getOwnPost($user, $status),
            'notOwn' => $this->getNotownPost($user, $status),
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
        yield "Author own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, "own", true];
        yield "Admin own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, "own", true];
        yield "Author own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, "own", false];
        yield "Admin own in review" => [UserFactory::anyAdmin(), PostStatus::IN_REVIEW, "own", false];
        yield "Author own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, "own", false];
        yield "Admin own published" => [UserFactory::anyAdmin(), PostStatus::PUBLISHED, "own", false];
        yield "Author own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, "own", false];
        yield "Admin own archived" => [UserFactory::anyAdmin(), PostStatus::ARCHIVED, "own", false];
        yield "Author own rejected" => [UserFactory::anyAuthor(), PostStatus::REJECTED, "own", false];
        yield "Admin own rejected" => [UserFactory::anyAdmin(), PostStatus::REJECTED, "own", false];
        yield "Author not own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, "notOwn", false];
        yield "Publisher not own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, "notOwn", false];
        yield "Admin not own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, "notOwn", true];
        yield "Author not own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, "notOwn", false];
        yield "Publisher not own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, "notOwn", false];
        yield "Admin not own in-review" => [UserFactory::anyAdmin(), PostStatus::IN_REVIEW, "notOwn", false];
        yield "Author not own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, "notOwn", false];
        yield "Publisher not own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, "notOwn", false];
        yield "Admin not own published" => [UserFactory::anyAdmin(), PostStatus::PUBLISHED, "notOwn", false];
        yield "Author not own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, "notOwn", false];
        yield "Publisher not own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, "notOwn", false];
        yield "Admin not own archived" => [UserFactory::anyAdmin(), PostStatus::ARCHIVED, "notOwn", false];
        yield "Author not own rejected" => [UserFactory::anyAuthor(), PostStatus::REJECTED, "notOwn", false];
        yield "Publisher not own rejected" => [UserFactory::anyAuthor(), PostStatus::REJECTED, "notOwn", false];
        yield "Admin not own rejected" => [UserFactory::anyAdmin(), PostStatus::REJECTED, "notOwn", false];
    }

    #[DataProvider('deletePostVisibilityProvider')]
    public function testDeletePostActionVisibility(User $user, PostStatus $status, string $whichPost, bool $visible): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = match($whichPost) {
            "own" => $this->getOwnPost($user, $status),
            'notOwn' => $this->getNotownPost($user, $status),
        };

        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(Action::DELETE, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(Action::DELETE, $post->getId());
        }
    }

    public static function inReviewPostVisibilityProvider(): iterable
    {
        yield "Author own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, "own", true];
        yield "Admin own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, "own", true];
        yield "Author own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, "own", false];
        yield "Admin own in review" => [UserFactory::anyAdmin(), PostStatus::IN_REVIEW, "own", false];
        yield "Author own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, "own", false];
        yield "Admin own published" => [UserFactory::anyAdmin(), PostStatus::PUBLISHED, "own", false];
        yield "Author own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, "own", false];
        yield "Admin own archived" => [UserFactory::anyAdmin(), PostStatus::ARCHIVED, "own", false];
        yield "Author own rejected" => [UserFactory::anyAuthor(), PostStatus::REJECTED, "own", false];
        yield "Admin own rejected" => [UserFactory::anyAdmin(), PostStatus::REJECTED, "own", false];

        yield "Author not own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, "notOwn", false];
        yield "Publisher not own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, "notOwn", false];
        yield "Admin not own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, "notOwn", true];
        yield "Author not own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, "notOwn", false];
        yield "Publisher not own in-review" => [UserFactory::anyAuthor(), PostStatus::IN_REVIEW, "notOwn", false];
        yield "Admin not own in-review" => [UserFactory::anyAdmin(), PostStatus::IN_REVIEW, "notOwn", false];
        yield "Author not own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, "notOwn", false];
        yield "Publisher not own published" => [UserFactory::anyAuthor(), PostStatus::PUBLISHED, "notOwn", false];
        yield "Admin not own published" => [UserFactory::anyAdmin(), PostStatus::PUBLISHED, "notOwn", false];
        yield "Author not own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, "notOwn", false];
        yield "Publisher not own archived" => [UserFactory::anyAuthor(), PostStatus::ARCHIVED, "notOwn", false];
        yield "Admin not own archived" => [UserFactory::anyAdmin(), PostStatus::ARCHIVED, "notOwn", false];
        yield "Author not own rejected" => [UserFactory::anyAuthor(), PostStatus::REJECTED, "notOwn", false];
        yield "Publisher not own rejected" => [UserFactory::anyAuthor(), PostStatus::REJECTED, "notOwn", false];
        yield "Admin not own rejected" => [UserFactory::anyAdmin(), PostStatus::REJECTED, "notOwn", false];
    }

    #[DataProvider('inReviewPostVisibilityProvider')]
    public function testRequestReviewPostActionVisibility(User $user, PostStatus $status, string $whichPost, bool $visible): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = match($whichPost) {
            "own" => $this->getOwnPost($user, $status),
            'notOwn' => $this->getNotownPost($user, $status),
        };
        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists('request_review', $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists('request_review', $post->getId());
        }
    }

    protected function getControllerFqcn(): string
    {
        return PostCrudController::class;
    }

    private function getOwnPost(User $user, PostStatus $status): ?Post
    {
        $postRepository = $this->entityManager->getRepository(Post::class);

        $ownPosts = array_values(array_filter($postRepository->findBy(['status' => $status]), fn(Post $post) => $post->getAuthor()->getId() === $user->getId()));

        if (count($ownPosts) === 0) {
            return null;
        }

        return $ownPosts[0];
    }

    private function getNotOwnPost(User $user, PostStatus $status): ?Post
    {
        $postRepository = $this->entityManager->getRepository(Post::class);

        $notOwnPosts = array_values(array_filter($postRepository->findBy(['status' => $status]), fn(Post $post) => $post->getAuthor()->getId() !== $user->getId()));

        if (count($notOwnPosts) === 0) {
            return null;
        }

        return $notOwnPosts[0];
    }
}
