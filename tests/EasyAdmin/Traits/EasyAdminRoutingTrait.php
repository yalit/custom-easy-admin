<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Traits;

trait EasyAdminRoutingTrait
{
    protected function goToPostIndex(): void
    {
        $crawler = $this->client->getCrawler();
        $responsiveHeader = $crawler->filter('.responsive-header');

        if ($responsiveHeader->count() > 0 && $responsiveHeader->first()->isDisplayed()){
            $responsiveHeader->filter('button')->first()->click();
            $this->client->waitForVisibility('#main-menu');
        }

        $this->client->clickLink("Blog Posts");
    }
}