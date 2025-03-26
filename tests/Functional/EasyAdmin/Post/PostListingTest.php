<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Controller\Admin\PostCrudController;
use App\Entity\Enums\PostStatus;
use App\Entity\Post;
use App\Entity\User;
use App\Factory\UserFactory;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use App\Tests\Functional\Story\FunctionalTestStory;
use App\Tests\Functional\Story\InitialTestStateStory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Attribute\WithStory;

#[WithStory(InitialTestStateStory::class)]
class PostListingTest extends AbstractAppCrudTestCase
{
    use CrudTestIndexAsserts;

    public static function createUsersVisibilityProvider(): iterable
    {
        yield "Author" => [UserFactory::anyAuthor(), true];
        yield "Publisher" => [UserFactory::anyPublisher(), false];
        yield "Admin" => [UserFactory::anyAdmin(), true];
    }

    public static function allUsersProvider(): array
    {
        return FunctionalTestStory::oneUserOfEach();
    }

    public static function nonAdminUsersProvider(): array
    {
        return FunctionalTestStory::noAdminUsers();
    }

    public static function editOwnPostVisibilityProvider(): iterable
    {
        yield "Author" => [UserFactory::anyAuthor(), true];
        yield "Admin" => [UserFactory::anyAdmin(), true];
    }

    public static function editNotOwnPostVisibilityProvider(): iterable
    {
        yield "Author" => [UserFactory::anyAuthor(), false];
        yield "Publisher" => [UserFactory::anyAuthor(), false];
        yield "Admin" => [UserFactory::anyAdmin(), true];
    }

    #[DataProvider('allUsersProvider')]
    public function testPostIndexDisplays(User $user): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
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

    #[DataProvider('editOwnPostVisibilityProvider')]
    public function testOwnEditPostActionVisibility(User $user, bool $visible): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = $this->getOwnPost($user, PostStatus::DRAFT);
        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(Action::EDIT, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(Action::EDIT, $post->getId());
        }
    }

    #[DataProvider('editNotOwnPostVisibilityProvider')]
    public function testNotOwnEditPostActionVisibility(User $user, bool $visible): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = $this->getNotOwnPost($user, PostStatus::DRAFT);
        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(Action::EDIT, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(Action::EDIT, $post->getId());
        }
    }

    #[DataProvider('editOwnPostVisibilityProvider')]
    public function testOwnDeletePostActionVisibility(User $user, bool $visible): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = $this->getOwnPost($user, PostStatus::DRAFT);
        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(Action::DELETE, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(Action::DELETE, $post->getId());
        }
    }

    #[DataProvider('editNotOwnPostVisibilityProvider')]
    public function testNotOwnDeletePostActionVisibility(User $user, bool $visible): void
    {
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        self::assertResponseIsSuccessful();

        $post = $this->getNotOwnPost($user, PostStatus::DRAFT);
        self::assertNotNull($post);

        if ($visible) {
            $this->assertIndexEntityActionExists(Action::DELETE, $post->getId());
        } else {
            $this->assertIndexEntityActionNotExists(Action::DELETE, $post->getId());
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
