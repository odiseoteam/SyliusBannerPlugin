<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class BannerType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.ui.enabled',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => BannerTranslationType::class,
                'label' => 'odiseo_sylius_banner_plugin.form.banner.translations',
            ])
            ->add('taxons', TaxonAutocompleteChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'label' => 'odiseo_sylius_banner_plugin.form.banner.taxons',
            ])
            ->add('channels', ChannelChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'label' => 'odiseo_sylius_banner_plugin.form.banner.channels',
            ])
        ;
    }
}
