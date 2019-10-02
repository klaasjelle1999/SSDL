<?php


namespace App\FormType;


use App\FormData\UserCreateFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                ],
                'label' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Lid' => "ROLE_USER",
                    'Beheerder' => "ROLE_ADMIN",
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                ],
                'label' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'De wachtwoorden komen niet overeen',
                'first_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Wachtwoord'
                    ],
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Bevestig wachtwoord'
                    ],
                ],
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Naam',
                ],
                'label' => false,
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Man' => 'Male',
                    'Vrouw' => 'Female'
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'data_class' => UserCreateFormData::class
        ]);
    }
}