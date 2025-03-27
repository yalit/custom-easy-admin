<?php

namespace App\Voter;

use App\Entity\Enums\PostStatus;
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
    public const string REQUEST_REVIEW = 'post_request_review';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::REQUEST_REVIEW], true)
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

        return match ($attribute) {
            self::VIEW => $this->voteOnShow($user),
            self::CREATE => $this->voteOnCreate($user),
            self::EDIT, self::DELETE => $this->voteOnEdit($user, $subject),
            self::REQUEST_REVIEW => $this->voteOnRequestReview($user, $subject),
            default => false,
        };
    }

    protected function voteOnShow(User $user): bool
    {
        if ($this->security->isGranted(UserRole::ADMIN->value, $user)) {
            return true;
        }

        return $this->security->isGranted(UserRole::AUTHOR->value, $user)
            || $this->security->isGranted(UserRole::PUBLISHER->value, $user);
    }

    protected function voteOnCreate(User $user): bool
    {
        if ($this->security->isGranted(UserRole::ADMIN->value, $user)) {
            return true;
        }

        return $this->security->isGranted(UserRole::AUTHOR->value, $user);
    }

    protected function voteOnEdit(User $user, Post $post): bool
    {
        if ($post->getStatus() !== PostStatus::DRAFT) {
            return false;
        }

        return $post->getAuthor()->getId() === $user->getId() || $this->security->isGranted(UserRole::ADMIN->value, $user);
    }

    protected function voteOnRequestReview(User $user, Post $post): bool
    {
        if ($post->getStatus() !== PostStatus::DRAFT) {
            return false;
        }

        return $post->getAuthor()->getId() === $user->getId() || $this->security->isGranted(UserRole::ADMIN->value, $user);
    }
}
