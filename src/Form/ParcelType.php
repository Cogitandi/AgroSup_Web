<?php

namespace App\Form;

use App\Entity\Parcel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ParcelType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('parcelNumber')
                ->add('cultivatedArea')
                ->add('fuelApplication')
                ->add('ArimrOperator', EntityType::class, array(
                    'class' => 'App:Operator',
                    'choices' => $options['operators'],
                    //'choice_label' => 'firstName',
                    'choice_label' => function ($operator) {
                        return $operator->getfirstName() . ' ' . $operator->getSurname();
                    },
                    'placeholder' => 'no payments',
                    'required' => false,
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Parcel::class,
            'operators' => array(),
        ]);
    }

}

/*
 'choice_label' => 'firstName'
'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c');
            },
 
 
 
  ->add('operator', ChoiceType::class, [
                    'placeholder' => 'Operator',
                    'choice_value' => function (User $entity = null) {
                        return $entity ? $entity->getId() : '';
                    },])
 */