<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Process\Post\ArchivePost;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/post/{id}/archive', name: PostArchiveController::CRUD_ROUTE_NAME, methods: ['POST'])]
class PostArchiveController extends AbstractCrudController
{
    public const CRUD_ROUTE_NAME = 'admin_post_archive';

    public function __construct(private readonly MessageBusInterface $messageBus)
    {}

    public function __invoke(Post $post): Response
    {
        $this->messageBus->dispatch(new ArchivePost($post));

        return $this->redirectToRoute('admin_post_index');
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }
}
