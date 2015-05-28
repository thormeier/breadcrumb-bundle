<?php

namespace Thormeier\BreadcrumbBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
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
}
