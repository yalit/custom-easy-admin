<?php

declare(strict_types=1);

namespace Workflow\Actions;

use App\Entity\Post;
use Symfony\Component\Workflow\WorkflowInterface;
use Workflow\ActionInterface;
use Workflow\PostWorkflow;

class PostRequestReviewAction implements ActionInterface
{
    public function __construct(
        private readonly WorkflowInterface $postStateMachine,
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
