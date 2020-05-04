<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use App\Entity\Field;
use App\Entity\UserPlant;
use App\Entity\Plant;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class TreatmentType extends AbstractType {

    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager) {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $preSubmitKindChoosed = function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // if kind not choosed return
            if (!isset($data['kind']) || $data['kind'] == "") {
                return;
            }
            $choosedYP = $this->security->getUser()->getChoosedYearPlan()->getId();
            $form
                    ->add('data', DateType::class, [
                        'widget' => 'single_text'])
                    ->add('field', EntityType::class, [
                        'class' => Field::class,
                        'placeholder' => "Wybierz pole",
                        'choice_label' => 'name',
                        'query_builder' => function(EntityRepository $er) use ($choosedYP) {
                            return $er->createQueryBuilder('u')->where('u.yearPlan = ' . $choosedYP);
                        }]);
        };

        $seedKind = function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // if kind not choosed return
            if (!isset($data['kind']) || $data['kind'] == "") {
                return;
            }
            // if field not choosed return
            if (!isset($data['field']) || $data['field'] == "") {
                return;
            }
            // if seed is not seed return
            if ($data['kind'] != 'seed') {
                return;
            }

            $fieldId = $data['field'];
            $field = $this->entityManager->getRepository(Field::class)->find($fieldId);
            $plantOnField = $field->getPlant();
            $form
                    ->add('plant', EntityType::class, [
                        'class' => Plant::class,
                        'choices' => $this->security->getUser()->getUserPlantsPlant(),
                        'preferred_choices' => [$plantOnField],
                        'choice_label' => function ($userPlant) {
                            return $userPlant->getName();
                        }
                    ])
                    ->add('variety', TextType::class, [
                        'empty_data' => $field->getPlantVariety(),
                        'required' => false,
                    ])
                    ->add('dosePerHa', NumberType::class, [
                        'required' => false
                    ])
            ;
        };
        $fertilizerKind = function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // if kind not choosed return
            if (!isset($data['kind']) || $data['kind'] == "") {
                return;
            }
            // if field not choosed return
            if (!isset($data['field']) || $data['field'] == "") {
                return;
            }
            // if seed is not seed return
            if ($data['kind'] != 'fertilizer') {
                return;
            }

            $fieldId = $data['field'];
            $field = $this->entityManager->getRepository(Field::class)->find($fieldId);
            $plantOnField = $field->getPlant();
            $form
                    ->add('fertilizerName', TextType::class, [
                        'required' => false,
                    ])
                    ->add('dosePerHa', NumberType::class, [
                        'required' => false
                    ])
            ;
        };
        $sprayerKind = function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // if kind not choosed return
            if (!isset($data['kind']) || $data['kind'] == "") {
                return;
            }
            // if field not choosed return
            if (!isset($data['field']) || $data['field'] == "") {
                return;
            }
            // if seed is not seed return
            if ($data['kind'] != 'sprayer') {
                return;
            }

            $fieldId = $data['field'];
            $field = $this->entityManager->getRepository(Field::class)->find($fieldId);
            $plantOnField = $field->getPlant();
            $form
                    ->add('components', TextType::class, [
                        'required' => false,
                    ])
                    ->add('dosePerHa', NumberType::class, [
                        'required' => false
                    ])
            ;
        };
        $cultivationKind = function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // if kind not choosed return
            if (!isset($data['kind']) || $data['kind'] == "") {
                return;
            }
            // if field not choosed return
            if (!isset($data['field']) || $data['field'] == "") {
                return;
            }
            // if seed is not seed return
            if ($data['kind'] != 'cultivation') {
                return;
            }

            $fieldId = $data['field'];
            $field = $this->entityManager->getRepository(Field::class)->find($fieldId);
            $form
                    ->add('machineName', TextType::class, [
                        'required' => false,
                    ])
            ;
        };



        $builder
                ->add('kind', ChoiceType::class, [
                    'placeholder' => 'Wybierz rodzaj',
                    'choices' => [
                        'Siew' => "seed",
                        'Nawóz' => "fertilizer",
                        'Oprysk' => "sprayer",
                        'Uprawa' => "cultivation",
            ]])
                ->addEventListener(FormEvents::PRE_SUBMIT, $preSubmitKindChoosed)
                ->addEventListener(FormEvents::PRE_SUBMIT, $seedKind)
                ->addEventListener(FormEvents::PRE_SUBMIT, $fertilizerKind)
                ->addEventListener(FormEvents::PRE_SUBMIT, $sprayerKind)
                ->addEventListener(FormEvents::PRE_SUBMIT, $cultivationKind);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
        ]);
    }

}
