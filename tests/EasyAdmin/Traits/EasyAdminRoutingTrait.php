<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Traits;

trait EasyAdminRoutingTrait
{
    protected function goToPostIndex(): void
    {
        $this->openMenu();
        $this->client->clickLink("Blog Posts");
    }

    protected function goToCommentIndex(): void
    {
        $this->openMenu();
        $this->client->clickLink("Comments");
    }

    private function openMenu(): void
    {
        $crawler = $this->client->getCrawler();
        $responsiveHeader = $crawler->filter('.responsive-header');

        if ($responsiveHeader->count() > 0 && $responsiveHeader->first()->isDisplayed()){
            $responsiveHeader->filter('button')->first()->click();
            $this->client->waitForVisibility('#main-menu');
        }
    }
}