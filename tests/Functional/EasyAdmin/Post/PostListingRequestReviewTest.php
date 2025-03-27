<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Controller\Admin\Action\RequestReviewAction;
use App\Controller\Admin\PostCrudController;
use App\Entity\Enums\PostStatus;
use App\Entity\Post;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use App\Tests\Story\InitialTestStateStory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Attribute\WithStory;

#[WithStory(InitialTestStateStory::class)]
class PostListingRequestReviewTest extends AbstractAppCrudTestCase
{
    public function testRequestReviewOnDraftPost(): void
    {
        $urlGenerator = static::getContainer()->get(UrlGeneratorInterface::class);
        $translator = static::getContainer()->get(TranslatorInterface::class);
        $this->login();

        $post = $this->getPost(PostStatus::DRAFT);
        self::assertNotNull($post);

        $this->client->request(Request::METHOD_GET, $urlGenerator->generate(RequestReviewAction::CRUD_ROUTE_NAME, ['id' => $post->getId()]));
        self::assertResponseRedirects();
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        $postColumnStatusSelector = $this->getIndexEntityRowSelector($post->getId()) ." ". $this->getIndexColumnSelector('status', 'data');
        self::assertSelectorTextContains($postColumnStatusSelector, $translator->trans(PostStatus::IN_REVIEW->value));
    }

    protected function getControllerFqcn(): string
    {
        return PostCrudController::class;
    }

    private function getPost(PostStatus $status): ?Post
    {
        return $this->entityManager->getRepository(Post::class)->findOneBy(['status' => $status]);
    }
}
