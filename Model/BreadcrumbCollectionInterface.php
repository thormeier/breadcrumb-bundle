<?php

namespace Thormeier\BreadcrumbBundle\Model;

/**
 * Class BreadcrumbCollectionInterface
 */
interface BreadcrumbCollectionInterface
{
    /**
     * @param BreadcrumbInterface $breadcrumb
     *
     * @return $this
     */
    public function addBreadcrumb(BreadcrumbInterface $breadcrumb);

    /**
     * @param BreadcrumbInterface $newBreadcrumb
     * @param BreadcrumbInterface $positionBreadcrumb
     *
     * @return $this
     */
    public function addBreadcrumbBeforeCrumb(BreadcrumbInterface $newBreadcrumb, BreadcrumbInterface $positionBreadcrumb);

    /**
     * @param BreadcrumbInterface $newBreadcrumb
     * @param BreadcrumbInterface $positionBreadcrumb
     *
     * @return $this
     */
    public function addBreadcrumbAfterCrumb(BreadcrumbInterface $newBreadcrumb, BreadcrumbInterface $positionBreadcrumb);

    /**
     * @param BreadcrumbInterface $breadcrumb
     * @param int                 $position
     *
     * @return $this
     */
    public function addBreadcrumbAtPosition(BreadcrumbInterface $breadcrumb, $position);

    /**
     * @param BreadcrumbInterface $breadcrumb
     *
     * @return $this
     */
    public function addBreadcrumbToStart(BreadcrumbInterface $breadcrumb);

    /**
     * @return array
     */
    public function getAll();

    /**
     * @param string $route
     *
     * @return null|BreadcrumbInterface
     */
    public function getBreadcrumbByRoute($route);
}
