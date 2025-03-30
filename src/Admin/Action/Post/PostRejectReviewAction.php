<?php

namespace App\Admin\Action\Post;

use App\Controller\Admin\Post\PostRejectReviewController;
use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostRejectReviewAction extends AbstractCrudController
{
    public const string NAME = 'post_reject_review';
    public const string LABEL = 'Reject';

    public static function create(): Action
    {
        return Action::new(self::NAME, self::LABEL)
            ->linkToRoute(PostRejectReviewController::CRUD_ROUTE_NAME, fn(Post $post) => ['id' => $post->getId()]);;
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

}
