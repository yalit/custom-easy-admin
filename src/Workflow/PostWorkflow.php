<?php

declare(strict_types=1);

namespace App\Workflow;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class PostWorkflow implements EventSubscriberInterface
{
    const STATUS_DRAFT = "post.status.draft";
    const STATUS_IN_REVIEW = "post.status.in_review";
    const STATUS_PUBLISHED = "post.status.published";
    const STATUS_CANCELLED = "post.status.cancelled";

    const ACTION_TO_REVIEW = 'post.action.to_review';
    const ACTION_PUBLISH = 'post.action.publish';
    const ACTION_CANCEL = 'post.action.cancel';

    public function __construct(private Security $security)
    {
    }

    /**
     * @return Array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [];
    }
}
