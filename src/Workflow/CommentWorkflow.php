<?php

declare(strict_types=1);

namespace App\Workflow;

class CommentWorkflow
{
    const STATUS_CREATED = "comment.status.created";
    const STATUS_PUBLISHED = "comment.status.published";
    const STATUS_CANCELLED = "comment.status.cancelled";

    const ACTION_PUBLISH = 'comment.action.publish';
    const ACTION_CANCEL = 'comment.action.cancel';
}
