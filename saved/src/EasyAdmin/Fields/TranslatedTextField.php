<?php

declare(strict_types=1);

namespace Fields;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * Custom Field to allow for the translation of a Text Field
 */
class TranslatedTextField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): TextField
    {
        // Simple decoration of the TextField by specifying a custom template
        // in which I used the translator on the field value
        return (TextField::new($propertyName, $label))
            ->setTemplatePath('easyadmin/field/translated_text.html.twig')
            ;
    }
}
