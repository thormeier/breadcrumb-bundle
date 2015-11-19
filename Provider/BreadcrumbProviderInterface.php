<?php

namespace Thormeier\BreadcrumbBundle\Provider;

use Thormeier\BreadcrumbBundle\Model\BreadcrumbCollectionInterface;

/**
 * Interface BreadcrumbProviderInterface
 */
interface BreadcrumbProviderInterface
{
    /**
     * Get the BreadcrumCollection for the current requests route
     *
     * @return BreadcrumbCollectionInterface
     */
    public function getBreadcrumbs();
}
