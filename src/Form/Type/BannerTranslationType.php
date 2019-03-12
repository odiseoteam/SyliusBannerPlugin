<?php

namespace Odiseo\SyliusBannerPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BannerTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('imageFile', FileType::class, [
                'label' => 'odiseo_sylius_banner.form.banner.image',
            ])
            ->add('url', TextType::class, [
                'label' => 'odiseo_sylius_banner.form.banner.url',
                'required' => false
            ])
        ;
    }
}
