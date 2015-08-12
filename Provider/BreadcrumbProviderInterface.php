<?php

namespace Thormeier\BreadcrumbBundle\Provider;

use Thormeier\BreadcrumbBundle\Model\BreadcrumbCollectionInterface;

/**
 * Interface BreadcrumbProviderInterface
 */
interface BreadcrumbProviderInterface
{
    /**
     * @return BreadcrumbCollectionInterface
     */
    public function getBreadcrumbs();
}
