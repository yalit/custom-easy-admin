<?php

declare(strict_types=1);

namespace Workflow\Actions;

use App\Entity\Post;
use Symfony\Component\Workflow\WorkflowInterface;
use Workflow\ActionInterface;
use Workflow\PostWorkflow;

class PostPublishAction implements ActionInterface
{
    public function __construct(
        private readonly WorkflowInterface $postStateMachine,
    ) {
    }

    function supports($actionee): bool
    {
        return $actionee instanceof Post
            && $this->postStateMachine->can($actionee, PostWorkflow::ACTION_PUBLISH)
            ;
    }

    /**
     * @param Post $actionee
     */
    function execute($actionee): bool
    {
        $this->postStateMachine->apply($actionee, PostWorkflow::ACTION_PUBLISH);
        $actionee->setPublishedAt(new \DateTimeImmutable());
        return true;
    }
}
