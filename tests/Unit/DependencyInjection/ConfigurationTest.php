<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\DependencyInjection;

use Odiseo\SyliusBannerPlugin\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testItDefaultsToWebpImages(): void
    {
        self::assertSame(
            ['images' => ['format' => 'webp', 'quality' => 80, 'mobile_quality' => 75]],
            $this->process([]),
        );
    }

    public function testItAcceptsKeepingTheOriginalFormat(): void
    {
        $config = $this->process(['images' => ['format' => null]]);

        self::assertSame(['format' => null, 'quality' => 80, 'mobile_quality' => 75], $config['images']);
    }

    /** @dataProvider provideQualitiesOutOfRange */
    public function testItRejectsAQualityOutOfRange(string $option, int $quality): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->process(['images' => [$option => $quality]]);
    }

    /** @return iterable<string, array{string, int}> */
    public static function provideQualitiesOutOfRange(): iterable
    {
        yield 'quality below the minimum' => ['quality', 0];
        yield 'quality above the maximum' => ['quality', 101];
        yield 'mobile quality below the minimum' => ['mobile_quality', 0];
        yield 'mobile quality above the maximum' => ['mobile_quality', 101];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private function process(array $config): array
    {
        /** @var array<string, mixed> $processed */
        $processed = (new Processor())->processConfiguration(new Configuration(), [$config]);

        return $processed;
    }
}
