<?php

namespace Thormeier\BreadcrumbBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RoutingLoaderCompilerPass
 */
class RoutingLoaderCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $routingLoaderDefinition = $container->getDefinition('routing.loader');

        $container->setDefinition('thormeier_breadcrumb.routing.attach_breadcrumb_loader.inner', $routingLoaderDefinition);

        $container->setAlias('routing.loader', 'thormeier_breadcrumb.routing.attach_breadcrumb_loader');
    }
}
