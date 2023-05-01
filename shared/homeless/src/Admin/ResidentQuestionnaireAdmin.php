<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Admin;

use App\Controller\CRUDController;
use App\Entity\ResidentQuestionnaire;
use App\Service\ResidentQuestionnaireConverter;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag(name: 'sonata.admin', attributes: [
    'code' => 'app.resident_questionnaire.admin',
    'controller' => CRUDController::class,
    'label' => 'resident_questionnaire',
    'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
    'manager_type' => 'orm',
    'model_class' => ResidentQuestionnaire::class,
])]

class ResidentQuestionnaireAdmin extends AbstractAdmin
{
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'typeId',
    ];

    public function __construct(
        private readonly ResidentQuestionnaireConverter $converter,
    ) {
        parent::__construct();
    }

    /**
     * При создании анкеты, в транзакции создаём её копию в новом формате.
     */
    public function postPersist(object $object): void
    {
        if (!$object instanceof ResidentQuestionnaire) {
            return;
        }

        $this->entityManager->wrapInTransaction(function (EntityManager $em) use ($object): void {
            $cfr = $this->converter->lockClientForm($object);
            $this->converter->createOrUpdateClientFormResponse($object, $cfr);
            $em->flush();
        });
    }

    /**
     * При обновлении анкеты, в транзакции создаём или обновляем её копию в новом формате.
     */
    public function postUpdate(object $object): void
    {
        if (!$object instanceof ResidentQuestionnaire) {
            return;
        }

        $this->entityManager->wrapInTransaction(function (EntityManager $em) use ($object): void {
            $cfr = $this->converter->lockClientForm($object);
            $this->converter->createOrUpdateClientFormResponse($object, $cfr);
        });
    }

    /**
     * При удалении анкеты, в транзакции удалить её копию в новом формате.
     */
    public function preRemove(object $object): void
    {
        if (!$object instanceof ResidentQuestionnaire) {
            return;
        }

        $this->entityManager->wrapInTransaction(function (EntityManager $em) use ($object): void {
            $this->converter->lockClientForm($object);
            $this->converter->deleteClientFormResponse($object);
        });
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('typeId', ChoiceType::class, [
                'label' => 'Тип',
                'choices' => ResidentQuestionnaire::$types,
                'required' => false,
            ])
            ->add('isDwelling', CheckboxType::class, [
                'label' => 'Проживает в жилом помещении',
                'required' => false,
            ])
            ->add('roomTypeId', ChoiceType::class, [
                'label' => 'Тип помещения',
                'choices' => ResidentQuestionnaire::$roomTypes,
                'required' => false,
            ])
            ->add('isWork', CheckboxType::class, [
                'label' => 'Работает?',
                'required' => false,
            ])
            ->add('isWorkOfficial', CheckboxType::class, [
                'label' => 'Официальная работа?',
                'required' => false,
            ])
            ->add('isWorkConstant', CheckboxType::class, [
                'label' => 'Постоянная работа?',
                'required' => false,
            ])
            ->add('changedJobsCountId', ChoiceType::class, [
                'label' => 'Сколько сменил работ',
                'choices' => ResidentQuestionnaire::$changedJobsCounts,
                'required' => false,
            ])
            ->add('reasonForTransitionIds', ChoiceType::class, [
                'label' => 'Причина перехода на другую работу',
                'multiple' => true,
                'choices' => ResidentQuestionnaire::$reasonForTransitions,
                'required' => false,
            ])
            ->add('reasonForPetitionIds', ChoiceType::class, [
                'label' => 'Причина обращения',
                'multiple' => true,
                'choices' => ResidentQuestionnaire::$reasonForPetition,
                'required' => false,
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('type', null, [
                'label' => 'Тип',
            ])
            ->add('isFull', FieldDescriptionInterface::TYPE_BOOLEAN, [
                'label' => 'Заполнено',
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }
}
