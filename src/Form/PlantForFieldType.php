<?php

namespace App\Form;

use App\Entity\Field;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlantForFieldType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $plantList = array();
        foreach($options['userPlantList'] as $userPlant ) {
            array_push($plantList,$userPlant->getPlant() );
        }
        $builder
                ->add('plant', EntityType::class,
                        [
                            'class' => 'App:Plant',
//                            'query_builder' => function (EntityRepository $er) {
//                                return $er->createQueryBuilder('p');
//                            },
                            'choices' => $plantList,
                            'choice_label' => 'name',
                            'required' => false,
                            'placeholder' => 'not defined',
                            'label' => false,
                ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Field::class,
            'userPlantList' => array(),
        ]);
    }

}
