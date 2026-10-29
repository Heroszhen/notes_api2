<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Actress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ActressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $countries = array_values(Countries::getNames('fr'));
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom *',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'empty_data' => '',
                'required' => false,
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays *',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'placeholder' => 'Choisissez un pays',
                'choice_translation_locale' => 'fr',
                'empty_data' => '',
                'choices' => array_combine($countries, $countries),
                'preferred_choices' => ['États-Unis', 'Chine', 'Royaume-Uni'],
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Date de naissance',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => false,
                'invalid_message' => 'Merci d\'entrer une date valide.',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'ckeditor form-control',
                    'rows' => 4,
                ],
                'empty_data' => '',
                'required' => false,
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control photo-input',
                    'accept' => 'image/*',
                ],
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Image(
                        maxSize: '10M',
                        mimeTypes: ['image/*'],
                        mimeTypesMessage: 'Formats acceptés : image.',
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Actress::class,
        ]);
    }
}
