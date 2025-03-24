<?php

declare(strict_types=1);

namespace Workflow;

use;

interface ActionerInterface
{
    function addAction(ActionInterface $action): void;

    /**
     * @param Array<ActionInterface> $actions
     */
    function addActions(array $actions): void;


    /**
     * @param class-string $actionName
     */
    function can(string $actionName, object $actionee): bool;


    /**
     * @param class-string $actionName
     */
    function execute(string $actionName, object $actionee, bool $flush = true): bool;
}
