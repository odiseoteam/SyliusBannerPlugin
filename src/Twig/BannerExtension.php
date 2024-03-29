<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BannerExtension extends AbstractExtension
{
    public function __construct(
        private ?string $slider,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('banner_slider_name', [$this, 'getBannerSliderName']),
        ];
    }

    public function getBannerSliderName(): string
    {
        return $this->slider !== null ? $this->slider : 'default';
    }

    public function getName(): string
    {
        return 'banner';
    }
}
