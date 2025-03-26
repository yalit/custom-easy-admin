<?php

namespace App\Voter\Admin;

use App\Entity\Enums\UserRole;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const string EDIT = 'user_edit';
    public const string CREATE = 'user_create';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (in_array($attribute, [self::EDIT], true)
            && $subject instanceof User) || $attribute === self::CREATE;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $this->security->isGranted(UserRole::ADMIN->value);
    }
}
