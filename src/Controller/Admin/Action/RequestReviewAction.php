<?php

namespace App\Controller\Admin\Action;

use App\Entity\Post;
use App\Process\Post\RequestReview;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/post/{id}/requestReview', name: RequestReviewAction::CRUD_ROUTE_NAME, methods: ['GET'])]
class RequestReviewAction extends AbstractCrudController
{
    public const string NAME = 'request_review';
    public const string CRUD_ROUTE_NAME = 'admin_post_request_review';
    public const string LABEL = 'Request Review';

    public function __construct(private readonly MessageBusInterface $messageBus)
    {}

    public static function create(): Action
    {
        return Action::new(self::NAME, self::LABEL)
            ->linkToRoute(self::CRUD_ROUTE_NAME, fn(Post $post) => ['id' => $post->getId()]);;
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function __invoke(Post $post): Response
    {
        $this->messageBus->dispatch(new RequestReview($post));
        $this->addFlash("success", sprintf("The post %s has been sent for review", $post->getTitle()));

        return $this->redirectToRoute('admin_post_index');
    }

}
