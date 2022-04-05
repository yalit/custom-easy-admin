<?php

declare(strict_types=1);

namespace App\Workflow\Actions;

use App\Entity\Post;
use App\Entity\UserRoles;
use App\Workflow\ActionInterface;
use App\Workflow\PostWorkflow;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\WorkflowInterface;

class PostPublishAction implements ActionInterface
{
    public function __construct(
        private WorkflowInterface $postStateMachine,
        private Security $security
    ) {
    }

    function supports($actionee): bool
    {
        return $actionee instanceof Post
            && $this->postStateMachine->can($actionee, PostWorkflow::ACTION_PUBLISH)
            && $this->security->isGranted(UserRoles::ROLE_PUBLISHER)
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
