<?php

declare(strict_types=1);

namespace App\Workflow\Actions;

use App\Entity\Comment;
use App\Workflow\ActionInterface;
use App\Workflow\CommentWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;

class CommentPublishAction implements ActionInterface
{
    public function __construct(
        private WorkflowInterface $commentStateMachine,
    ) {
    }

    function supports($actionee): bool
    {
        return $actionee instanceof Comment
            && $this->commentStateMachine->can($actionee, CommentWorkflow::ACTION_PUBLISH)
            ;
    }

    /**
     * @param Comment $actionee
     */
    function execute($actionee): bool
    {
        $this->commentStateMachine->apply($actionee, CommentWorkflow::ACTION_PUBLISH);
        $actionee->setPublishedAt(new \DateTime());
        return true;
    }
}
