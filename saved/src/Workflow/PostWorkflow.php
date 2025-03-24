<?php

declare(strict_types=1);

namespace Workflow;

class PostWorkflow
{
    const STATUS_DRAFT = "post.status.draft";
    const STATUS_IN_REVIEW = "post.status.in_review";
    const STATUS_PUBLISHED = "post.status.published";
    const STATUS_CANCELLED = "post.status.cancelled";

    const ACTION_TO_REVIEW = 'post.action.to_review';
    const ACTION_PUBLISH = 'post.action.publish';
    const ACTION_CANCEL = 'post.action.cancel';
}
