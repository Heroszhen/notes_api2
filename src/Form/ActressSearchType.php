<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\ActressSearchModel;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActressSearchType extends SearchBarType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $countries = array_values(Countries::getNames('fr'));

        $builder
            ->add('country', ChoiceType::class, [
                'label' => 'Pays',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'placeholder' => 'Choisissez un pays',
                'choices' => array_combine($countries, $countries),
                'preferred_choices' => ['États-Unis', 'Chine', 'Royaume-Uni'],
                'empty_data' => '',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => ActressSearchModel::class,
        ]);
    }

    protected function getPlaceholder(): string
    {
        return 'Rechercher par nom, description';
    }
}
