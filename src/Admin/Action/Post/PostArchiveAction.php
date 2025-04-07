<?php

namespace App\Admin\Action\Post;

use App\Controller\Admin\Post\PostArchiveController;
use App\Controller\Admin\Post\PostRequestReviewController;
use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostArchiveAction extends AbstractCrudController
{
    public const NAME = 'post_archive';
    public const LABEL = 'Archive';

    public static function create(): Action
    {
        return Action::new(self::NAME, self::LABEL)
            ->linkToRoute(PostArchiveController::CRUD_ROUTE_NAME, fn(Post $post) => ['id' => $post->getId()])
            ->displayAsForm()
            ;
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

}
