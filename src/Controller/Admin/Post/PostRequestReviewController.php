<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Process\Post\PostRequestReview;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/post/{id}/requestReview', name: PostRequestReviewController::CRUD_ROUTE_NAME, methods: ['GET'])]
class PostRequestReviewController extends AbstractCrudController
{
    public const string CRUD_ROUTE_NAME = 'admin_post_request_review';

    public function __construct(private readonly MessageBusInterface $messageBus)
    {}

    public function __invoke(AdminContext $context, Post $post): Response
    {
        $this->messageBus->dispatch(new PostRequestReview($post));
        $this->addFlash("success", sprintf("The post %s has been sent for review", $post->getTitle()));

        return $this->redirectToRoute('admin_post_index');
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }
}
