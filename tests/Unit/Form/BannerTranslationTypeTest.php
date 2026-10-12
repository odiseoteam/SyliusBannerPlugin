<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Form;

use Odiseo\SyliusBannerPlugin\Entity\BannerTranslation;
use Odiseo\SyliusBannerPlugin\Form\Type\BannerTranslationType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BannerTranslationTypeTest extends TestCase
{
    public function testTheImageIsRequiredForANewTranslation(): void
    {
        self::assertSame(['odiseo', 'odiseo_image_create'], $this->validationGroupsFor(new BannerTranslation()));
    }

    public function testTheImageIsRequiredWhenThereIsNoTranslationYet(): void
    {
        self::assertSame(['odiseo', 'odiseo_image_create'], $this->validationGroupsFor(null));
    }

    /** Otherwise every edit would ask for the image again. */
    public function testTheImageIsNotRequiredForAStoredTranslation(): void
    {
        $translation = new BannerTranslation();
        (new \ReflectionProperty(BannerTranslation::class, 'id'))->setValue($translation, 7);

        self::assertSame(['odiseo'], $this->validationGroupsFor($translation));
    }

    /** @return array<mixed> */
    private function validationGroupsFor(?BannerTranslation $translation): array
    {
        $resolver = new OptionsResolver();
        (new BannerTranslationType(BannerTranslation::class, ['odiseo']))->configureOptions($resolver);

        $validationGroups = $resolver->resolve()['validation_groups'];
        self::assertInstanceOf(\Closure::class, $validationGroups);

        $form = $this->createMock(FormInterface::class);
        $form->method('getData')->willReturn($translation);

        $groups = $validationGroups($form);
        self::assertIsArray($groups);

        return $groups;
    }
}
