<?php

namespace App\Admin\Filter;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class OwnPostFilter implements FilterInterface
{
    use FilterTrait;

    private Security $security;

    public static function new(Security $security, ?string $label = null): self
    {
        return new self()
            ->setFilterFqcn(__CLASS__)
            ->setLabel($label ?? 'Own posts')
            ->setProperty('author')
            ->setFormTypeOption('mapped', false)
            ->setFormType(CheckboxType::class)
            ->setSecurity($security);
    }

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (!$user) {
            return;
        }

        if (!$filterDataDto->getValue()) {
            return;
        }

        $queryBuilder->andWhere(sprintf("%s.%s = :author", $filterDataDto->getEntityAlias(), 'author'))
            ->setParameter("author", $user);
    }

    public function setSecurity(Security $security): OwnPostFilter
    {
        $this->security = $security;
        return $this;
    }
}
