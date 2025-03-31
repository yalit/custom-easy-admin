<?php

namespace App\Admin\Action\Post;

use App\Controller\Admin\Post\PostPublishController;
use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PublishPostAction extends AbstractCrudController
{
    public const string NAME = 'post_publish';
    public const string LABEL = 'Publish';

    public static function create(): Action
    {
        return Action::new(self::NAME, self::LABEL)
            ->linkToRoute(PostPublishController::CRUD_ROUTE_NAME, fn(Post $post) => ['id' => $post->getId()]);;
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

}
