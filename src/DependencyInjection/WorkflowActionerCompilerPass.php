<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use App\Workflow\WorkflowActioner;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WorkflowActionerCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (null === $actionerDefinition = $container->getDefinition(WorkflowActioner::class)){
            return;
        }

        $actionsTaggedIds = $container->findTaggedServiceIds('app.workflow.action');

        foreach ($actionsTaggedIds as $id => $tag){
            $actionerDefinition->addMethodCall('addAction', [new Reference($id)]);
        }
    }
}
