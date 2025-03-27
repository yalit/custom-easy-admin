<?php

namespace App\Admin\Action;

use App\Controller\Admin\Post\PostRequestReviewController;
use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RequestReviewAction extends AbstractCrudController
{
    public const string NAME = 'request_review';
    public const string LABEL = 'Request Review';

    public static function create(): Action
    {
        return Action::new(self::NAME, self::LABEL)
            ->linkToRoute(PostRequestReviewController::CRUD_ROUTE_NAME, fn(Post $post) => ['id' => $post->getId()]);;
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

}
