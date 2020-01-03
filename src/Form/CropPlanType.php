<?php

namespace App\Form;

use App\Entity\YearPlan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CropPlanType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('fields', CollectionType::class, [
            'entry_type' => PlantForFieldType::class,
            'label' => false,
            'entry_options' => ['label' => false],
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => YearPlan::class,
        ]);
    }

}
