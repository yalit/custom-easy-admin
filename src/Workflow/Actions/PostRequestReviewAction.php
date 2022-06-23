<?php

declare(strict_types=1);

namespace App\Workflow\Actions;

use App\Entity\Post;
use App\Workflow\ActionInterface;
use App\Workflow\PostWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;

class PostRequestReviewAction implements ActionInterface
{
    public function __construct(
        private WorkflowInterface $postStateMachine,
    ) {
    }

    function supports($actionee): bool
    {
        return $actionee instanceof Post
            && $this->postStateMachine->can($actionee, PostWorkflow::ACTION_TO_REVIEW)
            ;
    }

    /**
     * @param Post $actionee
     */
    function execute($actionee): bool
    {
        $this->postStateMachine->apply($actionee, PostWorkflow::ACTION_TO_REVIEW);
        $actionee->setInReviewAt(new \DateTimeImmutable());
        return true;
    }
}
