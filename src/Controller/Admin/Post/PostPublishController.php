<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Process\Post\PublishPost;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/post/{id}/publish', name: PostPublishController::CRUD_ROUTE_NAME, methods: ['GET'])]
class PostPublishController extends AbstractCrudController
{
    public const CRUD_ROUTE_NAME = 'admin_post_publish';

    public function __construct(private readonly MessageBusInterface $messageBus)
    {}

    public function __invoke(Post $post): Response
    {
        $this->messageBus->dispatch(new PublishPost($post));
        $this->addFlash('success', sprintf('Post %s published.', $post->getTitle()));

        return $this->redirectToRoute('admin_post_index');
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }
}
