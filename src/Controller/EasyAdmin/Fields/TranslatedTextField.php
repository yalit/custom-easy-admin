<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin\Fields;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TranslatedTextField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null)
    {
        return (TextField::new($propertyName, $label))
            ->setTemplatePath('easyadmin/field/translated_text.html.twig')
            ;
    }
}
