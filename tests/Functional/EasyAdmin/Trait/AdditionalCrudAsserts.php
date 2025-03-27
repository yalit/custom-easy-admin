<?php

namespace App\Tests\Functional\EasyAdmin\Trait;

trait AdditionalCrudAsserts
{
    protected function assertPageActionExists(string $action): void
    {
        $message ??= sprintf('The page action %s does not exist', $action);

        $selector = '.page-actions '.$this->getActionSelector($action);
        self::assertSelectorExists($selector, $message);
    }

    protected function assertPageActionNotExists(string $action): void
    {
        $message ??= sprintf('The page action %s does exist', $action);

        $selector = '.page-actions '.$this->getActionSelector($action);
        self::assertSelectorNotExists($selector, $message);
    }
}
