<?php

namespace App\Controller\EasyAdmin;

use App\Controller\EasyAdmin\Fields\TranslatedTextField;
use App\Entity\Post;
use App\Security\EasyAdmin\PostVoter;
use App\Workflow\Actions\PostCancelAction;
use App\Workflow\Actions\PostPublishAction;
use App\Workflow\NonExistentActionForWorkflowActioner;
use App\Workflow\WorkflowActioner;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;

final class PostCrudController extends AbstractCrudController
{
    public const STATUS_DATE_FORMAT = 'MMM dd, y HH:mm a';

    public function __construct(private WorkflowActioner $workflowActioner)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['publishedAt' => 'DESC'])
            // set for the entire entity (for any instance) the needed permission by using a Custom Voter
            ->setEntityPermission(PostVoter::SHOW)
            ;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)
            // adds a specific webpack assets for specifically this CRUD Controller
            // Target here is to add some js handling for the specific action buttons
            ->addWebpackEncoreEntry('easyadmin-post')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        // creates new actions (cfr private custom functions below)
        $publishAction = $this->getPublishAction('post_publish');
        $cancelAction = $this->getCancelAction('post_cancel');

        return $actions
            // add a new actions specifically on the Index page nowhere else
            ->add(Crud::PAGE_INDEX,  $publishAction)
            ->setPermission('post_publish', PostVoter::PUBLISH)
            ->add(Crud::PAGE_INDEX,  $cancelAction)
            ->setPermission('post_cancel', PostVoter::CANCEL)
            ->setPermission(Action::NEW, PostVoter::CREATE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // remove an existing action from the index page
            ->remove(Crud::PAGE_INDEX, Action::DELETE)

            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('title')
            ->add('author')
            ->add('publishedAt')
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
        yield AssociationField::new('author');
        yield AssociationField::new('comments')->onlyOnIndex();
        // Set up a custom field for the display of the status on the index
        yield TranslatedTextField::new('status')->hideOnForm();
        yield DateTimeField::new('statusDate', 'Status Date')
            ->setFormat(self::STATUS_DATE_FORMAT)
        ;
        yield AssociationField::new('tags')->hideOnIndex();
    }

    /**
     * Specific action linked to the post_cancel action created below
     * Any process can be triggered here using DI
     */
    public function postPublish(AdminContext $adminContext): Response
    {
        /** @var Post $post */
        // From the AdminContext, you have access to the current instance
        // Attention getEntity() provides an EntityDto not the actual instance
        $post = $adminContext->getEntity()->getInstance();
        try {
            // from the DI you're able as in any other Symfony controller to trigger specific processes
            $execution = $this->workflowActioner->execute(PostPublishAction::class, $post);
        } catch (NonExistentActionForWorkflowActioner $e) {
            $execution = false;
        }

        if ($execution) {
            $messageFlash = sprintf("Post %s has correctly been Published", $post->getTitle());
        } else {
            $messageFlash = sprintf("Post %s couldn't be Published", $post->getTitle());
        }

         // the EasyAdmin CRUD Controllers are extension of the AbstractCRUDController so you can use all the basic
         // functionalities from it (addFlash, render, redirect ...)
        $this->addFlash("success", $messageFlash);

        return $this->redirect($adminContext->getReferrer());
    }

    /**
     * Specific action linked to the post_cancel action created below
     * Any process can be triggered here using DI
     */
    public function postCancel(AdminContext $adminContext): Response
    {
        /** @var Post $post */
        $post = $adminContext->getEntity()->getInstance();
        try {
            $execution = $this->workflowActioner->execute(PostCancelAction::class, $post);
        } catch (NonExistentActionForWorkflowActioner $e) {
            $execution = false;
        }

        if ($execution) {
            $messageFlash = sprintf("Post %s has correctly been Cancelled", $post->getTitle());
        } else {
            $messageFlash = sprintf("Post %s couldn't be Cancelled", $post->getTitle());
        }

        $this->addFlash("success", $messageFlash);

        return $this->redirect($adminContext->getReferrer());
    }

    /**
     * Creates a simple EasyAdmin action referencing it by the name given in parameter
     */
    private function getPublishAction(string $name): Action
    {
        // the 'name' of the action is its id in the EasyAdmin referential ==> allows for update afterwards
        $publishAction = Action::new($name);
        $publishAction
            // this is the actual process behind the action => here a specific custom controller action (see above)
            ->linkToCrudAction('postPublish')
            // that is the label that will be used by EasyAdmin when displaying the call to action link
            // Translation is handled (cfr post.action.publish in the 'en' translation file
            ->setLabel('post.action.publish')
            // Each action can be displayed based on specific criteria
            // the function accepts a function taking as input the entity on which it should be done
            ->displayIf(
                fn($entity) => null !== $entity
                    && $this->workflowActioner->can(PostPublishAction::class, $entity)
            )
        ;
        return $publishAction;
    }

    /**
     * Creates a simple EasyAdmin action referencing it by the name given in parameter
     */
    private function getCancelAction(string $name): Action
    {
        $cancelAction = Action::new($name);
        $cancelAction
            ->linkToCrudAction('postCancel')
            ->setLabel('post.action.cancel')
            ->displayIf(
                fn($entity) => null !== $entity
                    && $this->workflowActioner->can(PostCancelAction::class, $entity)
            )
        ;
        return $cancelAction;
    }
}
