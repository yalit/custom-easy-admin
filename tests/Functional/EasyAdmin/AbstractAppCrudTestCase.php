<?php

namespace App\Tests\Functional\EasyAdmin;

use App\Controller\Admin\DashboardController;
use App\Story\Factory\UserFactory;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestSelectors;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractAppCrudTestCase extends AbstractCrudTestCase
{
    use ResetDatabase, Factories, CrudTestSelectors;

    abstract protected function getControllerFqcn(): string;

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function login(string $email = "admin@email.com", $password = UserFactory::PASSWORD): void
    {
        $this->client->request(Request::METHOD_GET, 'login');

        $this->client->submitForm('login_submit', [
            'email' => $email,
            'password' => $password,
        ]);
        $this->client->followRedirect();
        $this->client->followRedirect();
    }
}
