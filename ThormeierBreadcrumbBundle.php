<?php

namespace Thormeier\BreadcrumbBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Thormeier\BreadcrumbBundle\DependencyInjection\RoutingLoaderCompilerPass;
use Thormeier\BreadcrumbBundle\DependencyInjection\ThormeierBreadcrumbExtension;

/**
 * Breadcrumb bundle class
 *
 * @codeCoverageIgnore
 */
class ThormeierBreadcrumbBundle extends Bundle
{
    /**
     * @return ThormeierBreadcrumbExtension
     */
    public function getContainerExtension()
    {
        return new ThormeierBreadcrumbExtension();
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RoutingLoaderCompilerPass());
    }
}
