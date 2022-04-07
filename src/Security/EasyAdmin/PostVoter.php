<?php

declare(strict_types=1);

namespace App\Security\EasyAdmin;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserRoles;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PostVoter extends Voter
{
    public const CREATE = 'easyadmin_create';
    public const SHOW = 'easyadmin_show';
    public const PUBLISH = 'easyadmin_publish';
    public const CANCEL = 'easyadmin_cancel';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return ($subject instanceof Post
            && \in_array($attribute, [self::SHOW, self::PUBLISH, self::CANCEL], true)
            || \in_array($attribute, [self::CREATE], true)
        );
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::SHOW => $this->voteOnShow(),
            self::PUBLISH => $this->voteOnPublish(),
            self::CREATE => $this->voteOnCreate(),
            self::CANCEL => $this->voteOnCancel(),
            default => false,
        };
    }

    protected function voteOnShow(): bool
    {
        if ($this->security->isGranted(UserRoles::ROLE_AUTHOR) || $this->security->isGranted(UserRoles::ROLE_PUBLISHER)) {
            return true;
        }

        return false;
    }

    protected function voteOnPublish(): bool
    {
        if ($this->security->isGranted(UserRoles::ROLE_PUBLISHER)) {
            return true;
        }

        return false;
    }

    protected function voteOnCancel(): bool
    {
        if ($this->security->isGranted(UserRoles::ROLE_PUBLISHER)) {
            return true;
        }

        return false;
    }

    protected function voteOnCreate(): bool
    {
        if ($this->security->isGranted(UserRoles::ROLE_AUTHOR)) {
            return true;
        }

        return false;
    }
}
