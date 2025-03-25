<?php

namespace App\Factory;

use App\Entity\Enums\UserRoles;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public const PASSWORD = "Password123)";

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'fullName' => self::faker()->name(),
            'email' => self::faker()->email(),
            'username' => self::faker()->userName(),
            'plainPassword' => UserFactory::PASSWORD,
            'roles' => [],
        ];
    }

    public static function admin(): array|callable
    {
        return [
            'fullName' => "Administrator",
            'email' => "admin@email.com",
            'username' => "Admin",
            'plainPassword' => UserFactory::PASSWORD,
            'roles' => [UserRoles::ADMIN->value],
        ];
    }

    public static function publisher(int $n): array|callable
    {
        return [
            'fullName' => sprintf("Publisher %d", $n),
            'email' => sprintf("publisher_%d@email.com", $n),
            'username' => sprintf("publisher_%d", $n),
            'plainPassword' => UserFactory::PASSWORD,
            'roles' => [UserRoles::PUBLISHER->value],
        ];
    }

    public static function author(int $n): array|callable
    {
        return [
            'fullName' => sprintf("Author %d", $n),
            'email' => sprintf("author_%d@email.com", $n),
            'username' => sprintf("author_%d", $n),
            'plainPassword' => UserFactory::PASSWORD,
            'roles' => [],
        ];
    }
}
