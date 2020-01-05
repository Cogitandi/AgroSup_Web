<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Plant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Security;

class UserPlantsType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $checkBoxStatus = array();
        foreach ($options['plantList'] as $plant) {

            foreach ($options['userPlants'] as $userPlant) {

                if ($userPlant->getPlant() == $plant) {
                    array_push($checkBoxStatus, array('checked' => true));
                    break;
                } else if($options['userPlants']->last() == $userPlant) {
                    array_push($checkBoxStatus, array('checked' => false));
                }
            }
        }
        $builder
                ->add('Plants', ChoiceType::class, [
                    'mapped' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'label' => false,
                    'choices' => $options['plantList'],
                    'choice_label' => function(Plant $plant, $key, $value) {
                        return $plant->getName();
                    },
                    'choice_attr' => $checkBoxStatus,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class,
            'plantList' => array(),
            'userPlants' => array(),
        ]);
    }

}
