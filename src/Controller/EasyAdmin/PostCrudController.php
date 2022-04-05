<?php

namespace App\Controller\EasyAdmin;

use App\Controller\EasyAdmin\Fields\TranslatedTextField;
use App\Entity\Post;
use App\Workflow\Actions\PostPublishAction;
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
            ;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)
            ->addWebpackEncoreEntry('easyadmin-post')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $publishAction = Action::new('post_publish');
        $publishAction
            ->linkToCrudAction('postPublish')
            ->setLabel('post.action.publish')
            ->displayIf(fn($entity) => $this->workflowActioner->can(PostPublishAction::class, $entity))
        ;

        return $actions
            ->add(Crud::PAGE_INDEX,  $publishAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
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
        yield TranslatedTextField::new('status')->hideOnForm();
        yield DateTimeField::new('statusDate', 'Status Date')
            ->setFormat(self::STATUS_DATE_FORMAT)
        ;
        yield AssociationField::new('tags')->hideOnIndex();
    }


    public function postPublish(AdminContext $adminContext): Response
    {
        /** @var Post $post */
        $post = $adminContext->getEntity()->getInstance();
        try {
            $execution = $this->workflowActioner->execute(PostPublishAction::class, $post);
        } catch (\Exception $e) {
            $execution = false;
        }

        if ($execution) {
            $messageFlash = sprintf("Post %s has correctly been Published", $post->getTitle());
        } else {
            $messageFlash = sprintf("Post %s couldn't be Published", $post->getTitle());
        }

        $this->addFlash("success", $messageFlash);

        return $this->redirect($adminContext->getReferrer());
    }
}
