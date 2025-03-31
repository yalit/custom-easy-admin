<?php

namespace App\Listener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(event: Events::prePersist)]
readonly class UserPasswordListener
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    { }

    public function prePersist(PrePersistEventArgs $eventArgs): void
    {
        $user = $eventArgs->getObject();

        if (!$user instanceof User) {
            return;
        }

        $this->handleUserPasswordHashing($user);
    }

    public function preUpdate(User $user, PreUpdateEventArgs $eventArgs): void
    {
        $this->handleUserPasswordHashing($user);
    }

    private function handleUserPasswordHashing(User $user): void
    {
        $user->setPassword($this->hasher->hashPassword($user, $user->getPlainPassword()));
    }
}
