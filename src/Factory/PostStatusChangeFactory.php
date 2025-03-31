<?php

namespace App\Factory;

use App\Entity\Enums\PostStatus;
use App\Entity\PostStatusChange;
use Symfony\Bundle\SecurityBundle\Security;

readonly class PostStatusChangeFactory
{
    public function __construct(
        private Security $security,
    ) {}

    public function create(PostStatus $previousStatus, PostStatus $currentStatus): PostStatusChange
    {
        $statusChange = new PostStatusChange();
        $statusChange->setPreviousStatus($previousStatus);
        $statusChange->setCurrentStatus($currentStatus);
        $statusChange->setUser($this->security->getUser());

        return $statusChange;
    }
}
