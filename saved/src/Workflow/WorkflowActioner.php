<?php

declare(strict_types=1);

namespace Workflow;

use Doctrine\ORM\EntityManagerInterface;

class WorkflowActioner implements ActionerInterface
{
    /**
     * @var Array<ActionInterface>
     */
    private array $actions = [];

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function addAction(ActionInterface $action): void
    {
        if (!array_key_exists($action::class, $this->actions)){
            $this->actions[$action::class] = $action;
        }
    }

    public function addActions(array $actions): void
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }

    public function can(string $actionName, ?object $actionee): bool
    {
        if (!array_key_exists($actionName, $this->actions)) {
            throw new NonExistentActionForWorkflowActioner($actionName);
        }

        return $this->actions[$actionName]->supports($actionee);

    }

    public function execute(string $actionName, object $actionee, bool $flush = true): bool
    {
        if (!array_key_exists($actionName, $this->actions)) {
            throw new NonExistentActionForWorkflowActioner($actionName);
        }

        if ($executed = $this->executeAction($this->actions[$actionName], $actionee)) {
            $this->entityManager->persist($actionee);
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        return $executed;
    }

    private function executeAction(ActionInterface $action, object $actionee): bool
    {
        if (!$action->supports($actionee)){
            return false;
        }

        return $action->execute($actionee);
    }
}
