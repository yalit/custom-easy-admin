<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Action\RequestReviewAction;
use App\Controller\Admin\Field\EnumField;
use App\Entity\Enums\PostStatus;
use App\Entity\Post;
use App\Voter\PostVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PostCrudController extends AbstractCrudController
{
    public const string STATUS_DATE_FORMAT = 'MMM dd, y HH:mm a';

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityPermission(PostVoter::VIEW)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::NEW, PostVoter::CREATE)
            ->setPermission(Action::EDIT, PostVoter::EDIT)
            ->setPermission(Action::DELETE, PostVoter::DELETE)
            ->add(Crud::PAGE_INDEX, RequestReviewAction::create())
            ->setPermission(RequestReviewAction::NAME, PostVoter::REQUEST_REVIEW)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title');
        yield TextareaField::new('summary')->hideOnIndex()
            ->setNumOfRows(3)
            ->setHelp('Summaries can\'t contain Markdown or HTML contents; only plain text.');
        yield CodeEditorField::new('content')->hideOnIndex()
            ->setNumOfRows(15)->setLanguage('markdown')
            ->setHelp('Use Markdown to format the blog post contents. HTML is allowed too.');
        yield AssociationField::new('author')->hideOnForm();
        // yield AssociationField::new('comments')->onlyOnIndex();
        // Set up a custom field for the display of the status on the index
        yield EnumField::new('status')->hideOnForm()->setEnumClass(PostStatus::class);
        yield DateTimeField::new('statusDate', 'Status Date')
            ->setFormat(self::STATUS_DATE_FORMAT)
            ->hideOnForm();
        yield DateTimeField::new('createdAt', 'Creation Date')
            ->setFormat(self::STATUS_DATE_FORMAT)
            ->hideOnForm();
        yield AssociationField::new('tags');
        /*yield CollectionField::new('comments')
            // defines a specific custom block name (for the overwrite)
            ->setFormTypeOption('block_name', 'custom_collection_comments')
            // defines the type of Form of each entry of the Collection
            ->setEntryType(CommentType::class)
        ;*/
    }
}
