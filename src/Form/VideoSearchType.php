<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Actress;
use App\Enum\VideoTypeEnum;
use App\Model\VideoSearchModel;
use App\Repository\ActressRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoSearchType extends SearchBarType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('actress', EntityType::class, [
                'label' => 'Actrice',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'class' => Actress::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une actrice',
                'query_builder' => fn (ActressRepository $repository): QueryBuilder => $repository
                    ->createQueryBuilder('actress')
                    ->orderBy('actress.name', 'ASC'),
                'required' => false,
            ])
            ->add('videoType', EnumType::class, [
                'label' => 'Type de vidéo',
                'attr' => ['class' => 'form-select'],
                'class' => VideoTypeEnum::class,
                'label_attr' => ['class' => 'form-label'],
                'choice_label' => fn (VideoTypeEnum $type): string => $type->value,
                'placeholder' => 'Choisissez un type',
                'required' => false,
            ])
            ->add('website', SearchType::class, [
                'label' => 'Site',
                'empty_data' => '',
                'required' => false,
                'attr' => ['placeholder' => 'Recherche par site', 'class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => VideoSearchModel::class,
        ]);
    }

    protected function getPlaceholder(): string
    {
        return 'Rechercher par titre, description';
    }
}
