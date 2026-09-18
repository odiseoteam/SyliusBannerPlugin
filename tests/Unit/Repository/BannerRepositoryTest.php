<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Repository\BannerRepository;
use PHPUnit\Framework\TestCase;

final class BannerRepositoryTest extends TestCase
{
    public function testItOrdersEnabledBannersByPositionAndFallsBackToId(): void
    {
        $repository = $this->createRepository();

        $dql = $repository->findByEnabledQueryBuilder(null, null)->getDQL();

        self::assertStringContainsString('ORDER BY b.position ASC, b.id ASC', $dql);
    }

    private function createRepository(): BannerRepository
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->method('createQueryBuilder')
            ->willReturnCallback(static fn (): QueryBuilder => new QueryBuilder($entityManager))
        ;

        return new BannerRepository($entityManager, new ClassMetadata(Banner::class));
    }
}
