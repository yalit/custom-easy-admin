<?php


use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Fields\TranslatedTextField;
use Symfony\Component\HttpFoundation\Response;
use Voter\CommentVoter;
use Workflow\Actions\CommentCancelAction;
use Workflow\Actions\CommentPublishAction;
use Workflow\NonExistentActionForWorkflowActioner;
use Workflow\WorkflowActioner;

class CommentCrudController extends AbstractCrudController
{
    public function __construct(private readonly WorkflowActioner $workflowActioner)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ;
    }

    #[\Override]
    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)
            ->addWebpackEncoreEntry('easyadmin-comment')
            ;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('content')->setMaxLength(48)->onlyOnIndex();
        yield TextEditorField::new('content')->setNumOfRows(10)->hideOnIndex();
        yield AssociationField::new('author');
        yield AssociationField::new('post')->autocomplete();
        yield TranslatedTextField::new('status');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        // creates new actions (cfr private custom functions below)
        $publishAction = $this->getPublishAction('comment_publish');
        $cancelAction = $this->getCancelAction('comment_cancel');

        return $actions
            // add a new actions specifically on the Index page nowhere else
            ->add(Crud::PAGE_INDEX,  $publishAction)
            ->add(Crud::PAGE_DETAIL,  $publishAction)
            ->add(Crud::PAGE_EDIT,  $publishAction)
            ->setPermission('comment_publish', CommentVoter::PUBLISH)
            ->add(Crud::PAGE_INDEX,  $cancelAction)
            ->add(Crud::PAGE_DETAIL,  $cancelAction)
            ->add(Crud::PAGE_EDIT,  $cancelAction)
            ->setPermission('comment_cancel', CommentVoter::CANCEL)
            ->setPermission(Action::NEW, CommentVoter::CREATE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)

            ;
    }

    /**
     * Specific action linked to the comment_publish action created below
     * Any process can be triggered here using DI
     */
    public function commentPublish(AdminContext $adminContext): Response
    {
        /** @var Comment $comment */
        // From the AdminContext, you have access to the current instance
        // Attention getEntity() provides an EntityDto not the actual instance
        $comment = $adminContext->getEntity()->getInstance();
        try {
            // from the DI you're able as in any other Symfony controller to trigger specific processes
            $execution = $this->workflowActioner->execute(CommentPublishAction::class, $comment);
        } catch (NonExistentActionForWorkflowActioner) {
            $execution = false;
        }

        if ($execution) {
            $messageFlash = sprintf("Comment %d has correctly been Published", $comment->getId());
        } else {
            $messageFlash = sprintf("Comment %d couldn't be Published", $comment->getId());
        }

        // the EasyAdmin CRUD Controllers are extension of the AbstractCRUDController so you can use all the basic
        // functionalities from it (addFlash, render, redirect ...)
        $this->addFlash("success", $messageFlash);

        return $this->redirect($adminContext->getReferrer());
    }

    /**
     * Specific action linked to the comment_cancel action created below
     * Any process can be triggered here using DI
     */
    public function commentCancel(AdminContext $adminContext): Response
    {
        /** @var Comment $comment */
        // From the AdminContext, you have access to the current instance
        // Attention getEntity() provides an EntityDto not the actual instance
        $comment = $adminContext->getEntity()->getInstance();
        try {
            // from the DI you're able as in any other Symfony controller to trigger specific processes
            $execution = $this->workflowActioner->execute(CommentCancelAction::class, $comment);
        } catch (NonExistentActionForWorkflowActioner) {
            $execution = false;
        }

        if ($execution) {
            $messageFlash = sprintf("Comment %d has correctly been Cancelled", $comment->getId());
        } else {
            $messageFlash = sprintf("Comment %d couldn't be Cancelled", $comment->getId());
        }

        // the EasyAdmin CRUD Controllers are extension of the AbstractCRUDController so you can use all the basic
        // functionalities from it (addFlash, render, redirect ...)
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
            ->linkToCrudAction('commentPublish')
            // that is the label that will be used by EasyAdmin when displaying the call to action link
            // Translation is handled (cfr post.action.publish in the 'en' translation file
            ->setLabel('comment.action.publish')
            // Each action can be displayed based on specific criteria
            // the function accepts a function taking as input the entity on which it should be done
            ->displayIf(
                fn($entity) => null !== $entity
                    && $this->workflowActioner->can(CommentPublishAction::class, $entity)
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
            ->linkToCrudAction('commentCancel')
            ->setLabel('comment.action.cancel')
            ->displayIf(
                fn($entity) => null !== $entity
                    && $this->workflowActioner->can(CommentCancelAction::class, $entity)
            )
        ;
        return $cancelAction;
    }
}
