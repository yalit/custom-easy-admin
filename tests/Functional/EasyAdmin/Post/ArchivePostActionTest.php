<?php

namespace App\Tests\Functional\EasyAdmin\Post;

use App\Controller\Admin\Post\PostArchiveController;
use App\Controller\Admin\Post\PostCrudController;
use App\Entity\Enums\PostStatus;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArchivePostActionTest extends AbstractAppCrudTestCase
{
    public function testArchivePost(): void
    {
        /** @var AdminUrlGeneratorInterface $urlGenerator */
        $urlGenerator = static::getContainer()->get(AdminUrlGenerator::class);
        $translator = static::getContainer()->get(TranslatorInterface::class);
        $this->login();

        $post = $this->anyPost(PostStatus::PUBLISHED);
        self::assertNotNull($post);

        $url = $urlGenerator->setRoute(PostArchiveController::CRUD_ROUTE_NAME, ['id' => $post->getId()])->generateUrl();
        $this->client->request(Request::METHOD_POST, $url);
        self::assertResponseRedirects();
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        $postColumnStatusSelector = $this->getIndexEntityRowSelector($post->getId()) ." ". $this->getIndexColumnSelector('status', 'data');
        self::assertSelectorTextContains($postColumnStatusSelector, $translator->trans(PostStatus::ARCHIVED->value));
    }

    protected function getControllerFqcn(): string
    {
        return PostCrudController::class;
    }
}
