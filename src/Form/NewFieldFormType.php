<?php

namespace App\Form;

use App\Entity\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NewFieldFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Wprowadź nazwę',
                                ]),
                    ],
                ])
        ;
        $builder->add('parcels', CollectionType::class, [
            'entry_type' => ParcelType::class,
            'entry_options' => ['label' => false, 'operators' => $options['operators']],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Field::class,
            'operators' => array(),
        ]);
    }

}
