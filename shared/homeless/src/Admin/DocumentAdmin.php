<?php

namespace App\Admin;

use App\Entity\Document;
use App\Entity\DocumentType;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Документы',
    'model_class' => Document::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]

class DocumentAdmin extends BaseAdmin
{
    protected array $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected string $translationDomain = 'App';

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('type', EntityType::class, [
                'label' => 'Тип',
                'required' => true,
                'class' => 'App\Entity\DocumentType',
                'group_by' => function ($val, $key, $index) {
                    if (($val instanceof DocumentType) && $val->getType() == DocumentType::TYPE_REGISTRATION) {
                        return 'Для постановки на учет';
                    }

                    return 'Прочие';
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.type', 'ASC')
                        ->addOrderBy('t.name', 'ASC');
                },
            ])
            ->add('numberPrefix', null, [
                'label' => 'Серия',
            ])
            ->add('number', null, [
                'label' => 'Номер',
            ])
            ->add('issued', null, [
                'label' => 'Кем выдан',
            ])
            ->add('date', 'Sonata\Form\Type\DatePickerType', [
                'dp_default_date' => (new DateTime())->format('Y-m-d'),
                'format' => 'dd.MM.yyyy',
                'label' => 'Когда выдан',
                'required' => true,

            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('type', null, [
                'label' => 'Тип',
            ])
            ->add('type.type', 'number', [
                'label' => 'Для постановки на учет',
                'template' => '/admin/fields/document_type_type_list.html.twig'
            ])
            ->add('numberPrefix', null, [
                'label' => 'Серия',
            ])
            ->add('number', null, [
                'label' => 'Номер',
            ])
            ->add('issued', null, [
                'label' => 'Кем выдан',
            ])
            ->add('date', null, [
                'label' => 'Когда выдан',
            ])
            ->add('createdAt', 'date', [
                'label' => 'Когда добавлен',
            ])
            ->add('createdBy', null, [
                'label' => 'Кем добавлен',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }
}
