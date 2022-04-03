<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\EasyAdmin;

use App\Entity\User;
use App\Entity\UserRoles;
use App\Security\UserVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        $actions
            // Set the permission for more than 1 actions at one time ==> overwrites all the existing permissions
            ->setPermissions([
                Action::EDIT => UserVoter::EDIT, // Use of a standard Symfony Voter
                Action::DELETE => UserRoles::ROLE_ADMIN // Use of a global Symfony Role
            ])
            //Setting the permission uniquely for one single action
            //(can't be used before the setPermissions as setPermissions - above - overwrites everything)
            ->setPermission(Action::NEW, UserRoles::ROLE_ADMIN)
        ;

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
        yield AvatarField::new('email')->setIsGravatarEmail()->hideOnForm();
        yield TextField::new('fullName');
        yield TextField::new('username');
        yield EmailField::new('email');
        yield ChoiceField::new('roles')
            ->setChoices(UserRoles::getAllRoles())
        ;
    }
}
