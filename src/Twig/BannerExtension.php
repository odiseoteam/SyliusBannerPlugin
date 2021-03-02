<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BannerExtension extends AbstractExtension
{
    /** @var string|null */
    private $slider;

    public function __construct(
        ?string $slider
    ) {
        $this->slider = $slider;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('banner_slider_name', [$this, 'getBannerSliderName'])
        ];
    }

    /**
     * @return string
     */
    public function getBannerSliderName(): string
    {
        return $this->slider !== null ? $this->slider : 'default';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'banner';
    }
}
