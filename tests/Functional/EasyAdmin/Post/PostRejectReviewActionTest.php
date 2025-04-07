<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Controller\Admin\Post\PostCrudController;
use App\Controller\Admin\Post\PostPublishController;
use App\Controller\Admin\Post\PostRejectReviewController;
use App\Entity\Enums\PostStatus;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostRejectReviewActionTest extends AbstractAppCrudTestCase
{
    public function testPostRejectReviewAction(): void
    {
        /** @var AdminUrlGeneratorInterface $urlGenerator */
        $urlGenerator = static::getContainer()->get(AdminUrlGenerator::class);
        $translator = static::getContainer()->get(TranslatorInterface::class);
        $this->login();

        $post = $this->anyPost(PostStatus::IN_REVIEW);
        self::assertNotNull($post);

        $url = $urlGenerator->setRoute(PostRejectReviewController::CRUD_ROUTE_NAME, ['id' => $post->getId()])->generateUrl();
        $this->client->request(Request::METHOD_POST, $url);
        self::assertResponseRedirects();
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        $postColumnStatusSelector = $this->getIndexEntityRowSelector($post->getId()) ." ". $this->getIndexColumnSelector('status', 'data');
        self::assertSelectorTextContains($postColumnStatusSelector, $translator->trans(PostStatus::DRAFT->value));
    }

    protected function getControllerFqcn(): string
    {
        return PostCrudController::class;
    }
}
