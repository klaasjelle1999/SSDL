<?php


namespace App\FormType;


use App\FormData\PageCreateFormData;
use App\FormData\PageUpdateFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Pagina naam',
                ],
                'label' => false,
            ])
            ->add('text', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Pagina inhoud',
                ],
                'label' => false,
            ])
            ->add('visibleTo', ChoiceType::class, [
                'choices' => [
                    'Iedereen' => 'anyone',
                    'Leden' => 'member',
                    'Beheerder' => 'admin',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'data_class' => PageUpdateFormData::class,
        ]);
    }
}