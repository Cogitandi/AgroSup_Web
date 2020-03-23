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

class TreatmentType extends AbstractType {

    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $preSubmit = function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
           // print_r($data);
            if (!isset($data['kind']))
                return;
            if (!$data['kind'])
                return;
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
            ;
            if (!isset($data['field']))
                return;
            if (!$data['field'])
                return;
            $fieldId = $data['field'];
            $form
                    ->add('plant', EntityType::class, [
                        'class' => UserPlant::class,
                        'choice_label' => function ($category) {
                            return $category->getPlant()->getName();
                        },
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('p');
                        }
                    ])
                    ->add('variety', EntityType::class, [
                        'class' => Field::class,
                        'choice_label' => function ($category) {
                            return $category->getPlantVariety();
                        },
                        'query_builder' => function(EntityRepository $er) use ($fieldId) {
                            return $er->createQueryBuilder('f')
                                    ->where('f.id = ' . $fieldId);
                        }
                    ])
                    ->add('dosePerHa', NumberType::class)
            ;
            //->add('variety', TextType::class);
            //echo $data->getField()->getId();
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
                ->addEventListener(FormEvents::PRE_SUBMIT, $preSubmit);
        ;

        //$this->choosedKind($builder);
        //print_r("wynik".$builder->has('kind'));
    }

    public function choosedKind(FormBuilderInterface $builder) {

        $builder->get('kind')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $kind = $form->getData();
            if ($kind == "")
                return;

            $choosedYP = $this->security->getUser()->getChoosedYearPlan()->getId();
            $form->getParent()
                    ->add('data', DateType::class, [
                        'widget' => 'single_text'])
                    ->add('field', EntityType::class, [
                        'class' => Field::class,
                        'placeholder' => "Wybierz pole",
                        'choice_label' => 'name',
                        'query_builder' => function(EntityRepository $er) use ($choosedYP) {
                            return $er->createQueryBuilder('u')->where('u.yearPlan = ' . $choosedYP);
                        }]);
            ;
            switch ($kind) {
                case "seed":
                    $this->seedKind($form);
                    break;
                case "fertilizer":
                    $form->getParent()
                            ->add('dosePerHa', NumberType::class);
                    break;
                case "sprayer":
                    $form->getParent()
                            ->add('reasonForUse', TextType::class)
                            ->add('notes', TextType::class);
                    break;
                case "cultivation":
                    $form->getParent()
                            ->add('machineName', TextType::class);
                    break;
            }
        });
    }

    public function seedKind($form) {
        $form->getParent()
                ->add('dosePerHa', NumberType::class)
                ->add('plant', EntityType::class, [
                    'class' => UserPlant::class,
                    'choice_label' => function ($category) {
                        return $category->getPlant()->getName();
                    },
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                                ->where('u.user = ' . $this->security->getUser()->getId());
                    }
                ])
                ->add('variety', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
        ]);
    }

}
