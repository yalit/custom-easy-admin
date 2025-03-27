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
class PostEditTest extends AbstractAppCrudTestCase
{
    use AdditionalCrudAsserts;

    public static function dataForReviewActionDisplay(): iterable
    {
        yield "Author own draft" => [UserFactory::anyAuthor(), PostStatus::DRAFT, true, true];
        yield "Admin own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, true, true];
        yield "Admin not own draft" => [UserFactory::anyAdmin(), PostStatus::DRAFT, false, true];
    }

    #[DataProvider('dataForReviewActionDisplay')]
    public function testRequestReviewActionDisplayed(User $user, PostStatus $status, bool $ownPost, bool $visible): void
    {
        $this->login($user->getEmail());

        $post = match($ownPost) {
            true => PostFactory::anyOwned($user, $status),
            false => PostFactory::anyNotOwned($user, $status),
        };

        $this->client->request(Request::METHOD_GET, $this->generateEditFormUrl($post->getId()));
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
