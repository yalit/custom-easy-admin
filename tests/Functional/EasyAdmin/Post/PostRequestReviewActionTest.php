<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Controller\Admin\Post\PostCrudController;
use App\Controller\Admin\Post\PostRequestReviewController;
use App\Entity\Enums\PostStatus;
use App\Entity\Post;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use App\Tests\Story\InitialTestStateStory;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Attribute\WithStory;

#[WithStory(InitialTestStateStory::class)]
class PostRequestReviewActionTest extends AbstractAppCrudTestCase
{
    public function testRequestReviewOnDraftPost(): void
    {
        /** @var AdminUrlGeneratorInterface $urlGenerator */
        $urlGenerator = static::getContainer()->get(AdminUrlGenerator::class);
        $translator = static::getContainer()->get(TranslatorInterface::class);
        $this->login();

        $post = $this->getPost(PostStatus::DRAFT);
        self::assertNotNull($post);

        $url = $urlGenerator->setRoute(PostRequestReviewController::CRUD_ROUTE_NAME, ['id' => $post->getId()])->generateUrl();
        $this->client->request(Request::METHOD_GET, $url);
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
