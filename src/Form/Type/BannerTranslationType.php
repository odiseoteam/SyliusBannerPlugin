<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class BannerTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('imageFile', FileType::class, [
                'label' => 'odiseo_sylius_banner_plugin.form.banner.image',
            ])
            ->add('mobileImageFile', FileType::class, [
                'label' => 'odiseo_sylius_banner_plugin.form.banner.mobile_image',
            ])
            ->add('url', TextType::class, [
                'label' => 'odiseo_sylius_banner_plugin.form.banner.url',
                'required' => false
            ])
        ;
    }
}
