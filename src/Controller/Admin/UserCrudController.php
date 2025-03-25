<?php

namespace App\Controller\Admin;

use App\Entity\Enums\UserRoles;
use App\Entity\User;
use App\Voter\Admin\UserVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // Set the permission for more than 1 actions at one time ==> overwrites all the existing permissions
            ->setPermissions([
                Action::EDIT => UserVoter::EDIT, // Use of a standard Symfony Voter
                Action::DELETE => UserRoles::ADMIN->value // Use of a global Symfony Role
            ])
            //Setting the permission uniquely for one single action
            //(can't be used before the setPermissions as setPermissions - above - overwrites everything)
            ->setPermission(Action::NEW, UserRoles::ADMIN->value)
        ;

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
        yield TextField::new('fullName');
        yield TextField::new('username');
        yield EmailField::new('email');
        yield ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->setChoices(UserRoles::all())
            ->setRequired(false)
        ;
    }
}
