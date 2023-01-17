<?php

namespace App\Admin;

use App\Controller\ClientController;
use App\Entity\Client;
use App\Entity\DocumentFile;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'manager_type' => 'orm',
    'label' => 'Загруженные файлы',
    'model_class' => DocumentFile::class,
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
])]

class DocumentFileAdmin extends BaseAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected $translationDomain = 'App';

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('file', 'Vich\UploaderBundle\Form\Type\VichFileType', [
                'label' => 'Файл',
                'required' => true,
                'allow_delete' => false,
                'download_link' => true,
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
                'required' => false,
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('file', null, [
                'label' => 'Файл',
                'template' => '/admin/fields/list_file.html.twig',
            ])
            ->add('comment', null, [
                'label' => 'Комментарий',
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
