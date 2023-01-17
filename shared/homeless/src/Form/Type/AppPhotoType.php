<?php

namespace App\Form\Type;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Vich\UploaderBundle\Form\Type\VichImageType;

#[AutoconfigureTag(name: 'form.type', attributes: ['alias'=> 'app_photo'])]
class AppPhotoType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function getBlockPrefix(): string
    {
        return 'app_photo';
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }


    public function getParent(): string
    {
        return VichImageType::class;
    }
}
