<?php

namespace Thormeier\BreadcrumbBundle\Model;

/**
 * Breadcrumb collection that holds all breadcrumbs and allows special operations on it
 */
class BreadcrumbCollection
{
    /**
     * @var Breadcrumb[] Array of breadcrumbs
     */
    private $breadcrumbs = array();

    /**
     * @param Breadcrumb $breadcrumb
     *
     * @return $this
     */
    public function addBreadcrumb(Breadcrumb $breadcrumb)
    {
        $this->breadcrumbs[] = $breadcrumb;

        return $this;
    }

    /**
     * @param Breadcrumb $newBreadcrumb
     * @param Breadcrumb $positionBreadcrumb
     *
     * @return $this
     */
    public function addBreadcrumbBeforeCrumb(Breadcrumb $newBreadcrumb, Breadcrumb $positionBreadcrumb)
    {
        return $this->addBreadcrumbAtPosition($newBreadcrumb, ($this->getBreadcrumbPosition($positionBreadcrumb)));
    }

    /**
     * @param Breadcrumb $newBreadcrumb
     * @param Breadcrumb $positionBreadcrumb
     *
     * @return $this
     */
    public function addBreadcrumbAfterCrumb(Breadcrumb $newBreadcrumb, Breadcrumb $positionBreadcrumb)
    {
        return $this->addBreadcrumbAtPosition($newBreadcrumb, ($this->getBreadcrumbPosition($positionBreadcrumb) + 1));
    }

    /**
     * @param Breadcrumb $breadcrumb
     * @param int        $position
     *
     * @return $this
     */
    public function addBreadcrumbAtPosition(Breadcrumb $breadcrumb, $position)
    {
        array_splice($this->breadcrumbs, $position, 0, array($breadcrumb));

        return $this;
    }

    /**
     * @param Breadcrumb $breadcrumb
     *
     * @return $this
     */
    public function addBreadcrumbToStart(Breadcrumb $breadcrumb)
    {
        array_unshift($this->breadcrumbs, $breadcrumb);

        return $this;
    }

    /**
     * @return Breadcrumb[]
     */
    public function getAll()
    {
        return $this->breadcrumbs;
    }

    /**
     * @param string $route
     *
     * @return null|Breadcrumb
     */
    public function getBreadcrumbByRoute($route)
    {
        foreach ($this->breadcrumbs as $breadcrumb) {
            if ($route === $breadcrumb->getRoute()) {
                return $breadcrumb;
            }
        }

        return null;
    }

    /**
     * @param Breadcrumb $breadcrumb
     *
     * @return mixed
     */
    private function getBreadcrumbPosition(Breadcrumb $breadcrumb, $dump = false)
    {
        $position = array_search($breadcrumb, $this->breadcrumbs);
        if ($dump) { var_dump($position); exit; }
        if (false === $position) {
            throw new \InvalidArgumentException(sprintf(
                'Breadcrumb for route "%s" with label "%s" not found',
                $breadcrumb->getRoute(),
                $breadcrumb->getLabel()
            ));
        }

        return $position;
    }
}
