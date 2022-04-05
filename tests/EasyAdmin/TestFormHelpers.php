<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

use Facebook\WebDriver\WebDriverBy;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Panther\Client;

final class TestFormHelpers
{
    /**
     * @param array<ApplicationTestFormData> $formDatas
     */
    static public function submitFormWebDriver(
        Client $client,
        array $formDatas,
        string $submitClassName
    ): void {
        $crawler = $client->getCrawler();

        foreach ($formDatas as $formData) {
            if ($formData->isFilterNameId()){
                $inputField = $crawler->findElement(WebDriverBy::id(substr($formData->filterName, 1)));
            } else {
                $inputField = $crawler->findElements(WebDriverBy::className(substr($formData->filterName, 1)))[0];
            }
            $inputField->clear()->sendKeys($formData->value);
        }

        $crawler->filter($submitClassName)->eq(0)->click();
    }

    /**
     * @param array<ApplicationTestFormData> $formDatas
     */
    static public function submitFormKernelBrowser(
        KernelBrowser $client,
        array $formDatas,
        string $submitClassName
    ): void {
        $crawler = $client->getCrawler();

        $button = $crawler->filter($submitClassName);
        $form = $button->form();

        foreach ($formDatas as $formData) {
            $form[$formData->filterName] = $formData->value;
        }

        $client->submit($form);
    }
}
