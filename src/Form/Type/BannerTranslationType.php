<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Form\Type;

use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

final class BannerTranslationType extends AbstractResourceType
{
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
            ->add('mainText', TextType::class, [
                'label' => 'odiseo_sylius_banner_plugin.form.banner.main_text',
                'required' => false
            ])
            ->add('secondaryText', TextType::class, [
                'label' => 'odiseo_sylius_banner_plugin.form.banner.secondary_text',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form): array {
                $bannerTranslation = $form->getData();

                if (!$bannerTranslation instanceof BannerTranslationInterface || null === $bannerTranslation->getId()) {
                    return array_merge($this->validationGroups, ['odiseo_image_create']);
                }

                return $this->validationGroups;
            },
            'constraints' => array(
                new Valid()
            )
        ]);
    }
}
