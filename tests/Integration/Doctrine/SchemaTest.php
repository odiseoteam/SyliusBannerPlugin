<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Integration\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/** The database of the test application is built by running the migrations. */
final class SchemaTest extends KernelTestCase
{
    public function testTheMigrationsMatchTheMapping(): void
    {
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);

        $pending = (new SchemaTool($entityManager))->getUpdateSchemaSql($entityManager->getMetadataFactory()->getAllMetadata());

        self::assertSame([], array_values(array_filter(
            $pending,
            static fn (string $sql): bool => str_contains(strtolower($sql), 'odiseo_banner'),
        )));
    }
}
