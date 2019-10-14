<?php


namespace App\FormType;


use App\FormData\RecoverPasswordFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecoverPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The passwords must match.',
                'required' => true,
                'first_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Enter new password',
                        'class' => 'form-control',
                    ],
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Confirm new password',
                        'class' => 'form-control',
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'data_class' => RecoverPasswordFormData::class,
        ]);
    }
}