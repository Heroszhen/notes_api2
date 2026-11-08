<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Actress;
use App\Entity\Category;
use App\Entity\Video;
use App\Enum\VideoTypeEnum;
use App\Repository\ActressRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre *',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
                'empty_data' => '',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'ckeditor form-control',
                    'rows' => 4,
                ],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
            ])
            ->add('videoUrl', TextareaType::class, [
                'label' => 'Video url',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                ],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
                'empty_data' => '',
            ])
            ->add('website', TextareaType::class, [
                'label' => 'Site *',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                ],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
            ])
            ->add('visible', CheckboxType::class, [
                'label' => 'Visible',
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label me-2'],
                'required' => false,
            ])
            ->add('videoType', EnumType::class, [
                'label' => 'Type de vidéo *',
                'attr' => ['class' => 'form-select'],
                'class' => VideoTypeEnum::class,
                'label_attr' => ['class' => 'form-label'],
                'choice_label' => fn (VideoTypeEnum $type): string => $type->value,
            ])
            ->add('actress', EntityType::class, [
                'label' => 'Actrice *',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'class' => Actress::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une actrice',
                'autocomplete' => true,
                'query_builder' => fn (ActressRepository $repository): QueryBuilder => $repository
                    ->createQueryBuilder('actress')
                    ->orderBy('actress.name', 'ASC'),
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Catégories',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'autocomplete' => true,
                'required' => false,
                'query_builder' => fn (CategoryRepository $repository): QueryBuilder => $repository
                    ->createQueryBuilder('category')
                    ->orderBy('category.name', 'ASC'),
            ])
            ->add('photoFile', FileType::class, [
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
            ])
            ->add('videoFile', FileType::class, [
                'label' => 'Video fichier',
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'video/*',
                ],
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File(maxSize: '100M', mimeTypes: ['video/*']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
