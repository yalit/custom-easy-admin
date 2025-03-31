<?php

declare(strict_types=1);

namespace Workflow\Actions;

use App\Entity\Comment;
use Symfony\Component\Workflow\WorkflowInterface;
use Workflow\ActionInterface;
use Workflow\CommentWorkflow;

class CommentPublishAction implements ActionInterface
{
    public function __construct(
        private readonly WorkflowInterface $commentStateMachine,
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
