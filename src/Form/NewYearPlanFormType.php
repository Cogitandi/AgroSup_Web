<?php

namespace App\Form;

use App\Entity\YearPlan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class NewYearPlanFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startYear', IntegerType::class, [
                'attr' => [
                    'min' => 2005,
                    'max' => 2030
                ]])
            ->add('import', EntityType::class, array(
                    'class' => 'App:YearPlan',
                    'mapped' => false,
                    'choices' => $options['yearPlans'],
                    'choice_label' => function ($yearPlan) {
                        return $yearPlan->getStartYear() . '/' . $yearPlan->getEndYear();
                    },
                    'placeholder' => 'dont import',
                    'required' => false,
                ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => YearPlan::class,
            'yearPlans' => array(),
        ]);
    }
}
