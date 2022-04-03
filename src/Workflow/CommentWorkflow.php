<?php

declare(strict_types=1);

namespace App\Workflow;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class CommentWorkflow implements EventSubscriberInterface
{
    const STATUS_CREATED = "comment.status.created";
    const STATUS_PUBLISHED = "comment.status.published";
    const STATUS_CANCELLED = "comment.status.cancelled";

    const ACTION_PUBLISH = 'comment.action.publish';
    const ACTION_CANCEL = 'comment.action.cancel';

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
