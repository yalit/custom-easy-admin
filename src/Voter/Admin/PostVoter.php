<?php

namespace App\Voter\Admin;

use App\Entity\Enums\UserRole;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    public const string VIEW = 'post_show';
    public const string CREATE = 'post_create';
    public const string EDIT =  'post_edit';
    public const string DELETE = 'post_delete';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)
            && $subject instanceof Post) || $attribute === self::CREATE;
    }

    /**
     * @param Post $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(UserRole::ADMIN->value, $user)) {
            return true;
        }

        return match ($attribute) {
            self::VIEW => $this->voteOnShow($user),
            self::CREATE => $this->voteOnCreate($user),
            self::EDIT, self::DELETE => $this->voteOnEdit($user, $subject),
            default => false,
        };
    }

    protected function voteOnShow(User $user): bool
    {
        return $this->security->isGranted(UserRole::AUTHOR->value, $user)
            || $this->security->isGranted(UserRole::PUBLISHER->value, $user);
    }

    protected function voteOnCreate(User $user): bool
    {
        return $this->security->isGranted(UserRole::AUTHOR->value, $user);
    }

    protected function voteOnEdit(User $user, Post $post): bool
    {
        return $post->getAuthor()->getId() === $user->getId();
    }
}
