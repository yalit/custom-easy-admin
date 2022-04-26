<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Traits;

trait EasyAdminActionTrait
{
    public function clickOnElementRowAction(int $elementId, string $action): void
    {
        $crawler = $this->client->getCrawler();
        $elementRowSelector = sprintf('tr[data-id="%d"]', $elementId);

        $dropdownButton = $crawler->filter($elementRowSelector." .dropdown-toggle");
        $dropdownButton->getLocationOnScreenOnceScrolledIntoView();
        $dropdownButton->click();
        $this->client->waitForVisibility($elementRowSelector." .dropdown-menu");
        $dropdownButton->getLocationOnScreenOnceScrolledIntoView();

        $actionButton = $crawler->filter($elementRowSelector." .actions .action-".$action);
        $actionButton->click();
    }
}