<?php

namespace App\Form;

use App\Entity\Operator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AddOperatorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-ZąćęłńóśżźĄĆĘŁŃÓŚŻŹ]{2,20}$/',
                        'message' => 'niepoprawne imię',
                    ]),
                ]

            ])
            ->add('surname', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-ZąćęłńóśżźĄĆĘŁŃÓŚŻŹ]{2,40}$/',
                        'message' => 'niepoprawne nazwisko',
                    ]),
                ]

            ])
            ->add('arimrNumber', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[0-9]{11}+$/',
                        'message' => 'numer powinien zawierać 11 cyfr',
                    ]),
                ]

            ])
            ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Operator::class,
        ]);
    }
}
