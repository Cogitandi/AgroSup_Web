<?php

namespace App\Form;

use App\Entity\Field;
use App\Entity\Operator;
use App\Entity\YearPlan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class NewFieldFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name')
                ;
        $builder->add('parcels', CollectionType::class, [
            'entry_type' => ParcelType::class,
            'entry_options' => ['label' => false, 'operators'=>$options['operators']],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' =>false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Field::class,
            'operators' => array(),
        ]);
    }

}
