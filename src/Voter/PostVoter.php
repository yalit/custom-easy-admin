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
    public const VIEW = 'post_show';
    public const CREATE = 'post_create';
    public const EDIT =  'post_edit';
    public const DELETE = 'post_delete';
    public const REQUEST_REVIEW = 'post_request_review';
    public const PUBLISH = 'post_publish';
    public const REJECT_REVIEW = 'post_reject_review';
    public const ARCHIVE = 'post_archive';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::REQUEST_REVIEW, self::PUBLISH, self::REJECT_REVIEW, self::ARCHIVE], true)
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
            self::REJECT_REVIEW => $this->voteOnRejectReview($user, $subject),
            self::PUBLISH => $this->voteOnPublishPost($user, $subject),
            self::ARCHIVE => $this->voteOnArchivePost($user, $subject),
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

    protected function voteOnRejectReview(User $user, Post $post): bool
    {
        if ($post->getStatus() !== PostStatus::IN_REVIEW) {
            return false;
        }

        return $this->security->isGranted(UserRole::PUBLISHER->value, $user);
    }

    protected function voteOnPublishPost(User $user, Post $post): bool
    {
        if ($post->getStatus() !== PostStatus::IN_REVIEW) {
            return false;
        }

        return $this->security->isGranted(UserRole::PUBLISHER->value, $user);
    }

    protected function voteOnArchivePost(User $user, Post $post): bool
    {
        if ($post->getStatus() !== PostStatus::PUBLISHED) {
            return false;
        }

        return $post->getAuthor()->getId() === $user->getId() || $this->security->isGranted(UserRole::ADMIN->value, $user);
    }

}
