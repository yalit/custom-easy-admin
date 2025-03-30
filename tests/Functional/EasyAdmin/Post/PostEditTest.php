<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Controller\Admin\Post\PostCrudController;
use App\Entity\Enums\PostStatus;
use App\Entity\Enums\UserRole;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use App\Tests\Trait\AdditionalCrudAsserts;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;

class PostEditTest extends AbstractAppCrudTestCase
{
    use AdditionalCrudAsserts;

    public static function dataForReviewActionDisplay(): iterable
    {
        yield "Author own draft" => [UserRole::AUTHOR, PostStatus::DRAFT, true, true];
        yield "Admin own draft" => [UserRole::ADMIN, PostStatus::DRAFT, true, true];
        yield "Admin not own draft" => [UserRole::ADMIN, PostStatus::DRAFT, false, true];
    }

    #[DataProvider('dataForReviewActionDisplay')]
    public function testRequestReviewActionDisplayed(UserRole $userRole, PostStatus $status, bool $ownPost, bool $visible): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());

        $post = match($ownPost) {
            true => $this->anyPostOwned($user, $status),
            false => $this->anyPostNotOwned($user, $status),
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
