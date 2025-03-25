<?php

namespace App\Voter\Admin;

use App\Entity\Enums\UserRoles;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const string LIST = 'user_list';
    public const string VIEW = 'user_show';
    public const string EDIT = 'user_edit';
    public const string DELETE = 'user_delete';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::LIST, self::VIEW, self::EDIT, self::DELETE], true)
            && $subject instanceof User;
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

        if ($attribute === self::LIST) {
            return true;
        }

        return $this->security->isGranted(UserRoles::ADMIN->value);
    }
}
