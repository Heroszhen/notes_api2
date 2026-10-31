<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Actress;
use App\Model\ActressPhotoModel;
use App\Repository\ActressRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActressPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actress', EntityType::class, [
                'label' => 'Actrice*',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'class' => Actress::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une actrice',
                'query_builder' => fn (ActressRepository $repository): QueryBuilder => $repository
                    ->createQueryBuilder('actress')
                    ->orderBy('actress.name', 'ASC'),
            ])
            ->add('visible', CheckboxType::class, [
                'label' => 'Visible',
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label'],
                'required' => false,
            ])
            ->add('photos', FileType::class, [
                'label' => 'Photos*',
                'attr' => ['accept' => 'image/*', 'class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActressPhotoModel::class,
        ]);
    }
}
