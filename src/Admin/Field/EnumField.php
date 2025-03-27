<?php

declare(strict_types=1);

namespace App\Admin\Field;

use App\Controller\Admin\Field\EnumType;
use BackedEnum;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use http\Exception\InvalidArgumentException;

/**
 * Custom Field to allow for the display and usage of an Enum as a value
 */
class EnumField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        return new self()
            ->setProperty($propertyName)
            ->setLabel($label)
            // add a specific template for the field
            ->setTemplatePath('admin/field/enum_field.html.twig')
            //define the specific form type for the field
            ->setFormType(EnumType::class)
            ->setFormTypeOption('attr.class', 'width-inherit')
            //set the way the value is found
            ->setFormTypeOption('choice_label', static function (\BackedEnum $choice): string {
                return (string) $choice->value;
            })
            ;
    }

    public function setEnumClass(string $enumClass): self
    {
        if (!is_subclass_of($enumClass, BackedEnum::class)) {
            throw new InvalidArgumentException(sprintf("The enum class %s should be a Backed Enum", $enumClass));
        }
        $this->setFormTypeOption('class', $enumClass);

        return $this;
    }

}
