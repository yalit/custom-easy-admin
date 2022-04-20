<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class EasyAdminCommentCreationSubscriber implements EventSubscriberInterface
{

    public function __construct(private Security $security)
    {
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $this->setAuthor($eventArgs);
    }

    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->setAuthor($eventArgs);
    }

    private function setAuthor(LifecycleEventArgs $eventArgs): void
    {
        $comment = $eventArgs->getEntity();

        if (! $comment instanceof Comment) {
            return;
        }

        $user = $this->security->getUser();

        if (! $user instanceof User) {
            return;
        }

        $comment->setAuthor($user);
    }
}
