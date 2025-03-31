<?php

declare(strict_types=1);

namespace Workflow;

use Throwable;

class NonExistentActionForWorkflowActioner extends \Exception
{
    /**
     * @var string $message
     */
    protected $message = "The Action %s is not defined. Please consider implementing ActionInterface for %s";

    public function __construct(string $actionName, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf($this->message, $actionName, $actionName), $code, $previous);
    }
}
