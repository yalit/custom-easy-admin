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

    public const SHOW = 'easyadmin_show';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Post && \in_array($attribute, [self::SHOW], true);
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

        if (
            $this->security->isGranted(UserRoles::ROLE_PUBLISHER)
            || $this->security->isGranted(UserRoles::ROLE_AUTHOR)
        ) {
            return true;
        }

        return false;
    }
}
