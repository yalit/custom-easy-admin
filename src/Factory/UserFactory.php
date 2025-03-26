<?php

namespace App\Factory;

use App\Entity\Enums\UserRole;
use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;

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

    /**
     * @return Proxy<User>
     */
    public static function admin(): Proxy
    {
        return UserFactory::new()
            ->asAdmin()
            ->create()
        ;
    }

    /**
     * @return Proxy<User>[]
     */
    public static function publisher(string $name = "Publisher", int $nb = 5): array
    {
        return UserFactory::new()
            ->asPublisher()
            ->sequence(function() use ($name, $nb) {
                for ($i = 1; $i <= $nb; ++$i) {
                    yield UserFactory::setName(sprintf("%s %s", $name, $i));
                }
            })
            ->create()
        ;
    }

    /**
     * @return Proxy<User>[]
     */
    public static function author(string $name = "Author", int $nb = 5): array
    {
        return UserFactory::new()
            ->asAuthor()
            ->sequence(function() use ($name, $nb) {
                for ($i = 1; $i <= $nb ; ++$i) {
                    yield UserFactory::setName(sprintf("%s %s", $name, $i));
                }
            })
            ->create()
        ;
    }

    /**
     * @return Proxy<User>
     */
    public static function anyAdmin(): Proxy
    {
        return self::anyUserWithRole(UserRole::ADMIN) ?? self::admin();
    }

    /**
     * @return Proxy<User>
     */
    public static function anyAuthor(string $name = "Author"): Proxy
    {
        return self::anyUserWithRole(UserRole::AUTHOR) ?? self::author($name, 1)[0];
    }

    /**
     * @return Proxy<User>
     */
    public static function anyPublisher(string $name = "Publisher"): Proxy
    {
        return self::anyUserWithRole(UserRole::PUBLISHER) ?? self::publisher($name, 1)[0];
    }

    /**
     * @return Proxy<User> | null
     */
    private static function anyUserWithRole(UserRole $role): ?Proxy
    {
        $allUsers = UserFactory::all();

        $users = array_values(array_filter($allUsers, fn(User $u) => $u->hasRole($role->value)));

        if (count($users) === 0) {
            return null;
        }

        return $users[array_rand($users)];
    }

    private static function setName(string $name): array
    {
        $username = sprintf("%s", strtolower(str_replace(' ', '_', $name)));
        return [
                'fullName' => $name,
                'email' => sprintf("%s@email.com", $username),
                'username' => $username,
            ]
        ;
    }

    private function asAdmin(): self
    {
        return $this->with([
            'fullName' => 'Admin',
            'email' => "admin@email.com",
            'username' => "admin",
            'roles' => [UserRole::ADMIN->value],
        ]);
    }

    private function asPublisher(): self
    {
        return $this->with([
            'roles' => [UserRole::PUBLISHER->value],
        ]);
    }

    private function asAuthor(): self
    {
        return $this->with([
            'roles' => [UserRole::AUTHOR->value],
        ]);
    }

}
