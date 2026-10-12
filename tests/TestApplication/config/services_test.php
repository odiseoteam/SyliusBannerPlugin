<?php

declare(strict_types=1);

use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    if (str_starts_with($container->env(), 'test')) {
        $container->import(\dirname((string) (new \ReflectionClass(SharedStorageInterface::class))->getFileName(), 2) . '/Resources/config/services.xml');
        $container->import('@OdiseoSyliusBannerPlugin/tests/Behat/Resources/services.xml');
    }
};
