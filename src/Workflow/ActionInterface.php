<?php

declare(strict_types=1);

namespace App\Workflow;

interface ActionInterface
{
    function supports($actionee): bool;
    function execute($actionee): bool;
}