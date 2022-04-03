<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Entity\UserRoles;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{

    public const EDIT = 'edit';

    public function __construct(private Security $security)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, $subject): bool
    {
        // this voter is only executed on Post objects and for three specific permissions
        return $subject instanceof User && in_array($attribute, [self::EDIT], true);
    }

    /**
     * {@inheritdoc}
     *
     * @param User $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }

        return $this->security->isGranted(UserRoles::ROLE_ADMIN);
    }
}
