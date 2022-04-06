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
    public const PUBLISH = 'easyadmin_publish';
    public const CANCEL = 'easyadmin_cancel';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Post && \in_array($attribute, [self::SHOW, self::PUBLISH, self::CANCEL], true);
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

        if (in_array($attribute, [self::PUBLISH, self::CANCEL, self::SHOW])
            && $this->security->isGranted(UserRoles::ROLE_PUBLISHER)) {
            return true;
        }

        if ($attribute === self::SHOW && $this->security->isGranted(UserRoles::ROLE_AUTHOR)) {
            return true;
        }

        return false;
    }
}
