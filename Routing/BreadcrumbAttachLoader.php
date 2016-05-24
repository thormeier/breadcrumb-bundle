<?php

namespace Thormeier\BreadcrumbBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Attaches breadcrumb tree to every routes default config
 */
class BreadcrumbAttachLoader extends Loader
{
    /**
     * @var LoaderInterface
     */
    private $routerLoader;

    /**
     * Attaches breadcrumb tree to every routes default config
     *
     * @param LoaderInterface $routerLoader
     */
    public function __construct(LoaderInterface $routerLoader)
    {
        $this->routerLoader = $routerLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $routeCollection = $this->routerLoader->load($resource, $type);

        foreach ($routeCollection->all() as $key => $route) {
            if ($route->hasOption('breadcrumb')) {
                $route->setDefault(
                    '_breadcrumbs',
                    $this->getBreadcrumbs($route, $key, $routeCollection)
                );
            }
        }

        return $routeCollection;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return $this->routerLoader->supports($resource, $type);
    }

    /**
     * Builds an array of breadcrumbs for the given route recursively
     *
     * @param Route           $route
     * @param string          $routeKey
     * @param RouteCollection $routeCollection
     * @param array           $rawBreadcrumbsCollection
     *
     * @return array
     */
    private function getBreadcrumbs(Route $route, $routeKey, RouteCollection $routeCollection, $rawBreadcrumbsCollection = array())
    {
        $breadcrumbOptions = $route->getOption('breadcrumb');

        // No label, no crumb.
        if (false === isset($breadcrumbOptions['label'])) {
            throw new \InvalidArgumentException(sprintf(
                'Label for breadcrumb on route "%s" must be configured',
                $routeKey
            ));
        }

        $rawCrumb = array(
            'route' => $routeKey,
            'label' => $breadcrumbOptions['label'],
        );

        // If this route already is in the raw collection, there's likely a circular breadcrumb, which will cause memory exhaustion
        if (false !== array_search($rawCrumb, $rawBreadcrumbsCollection)) {
            throw new \LogicException(sprintf(
                'Circular breadcrumbs detected at route "%s"',
                $routeKey
            ));
        }

        // Add element to beginning of breadcrumbs
        array_unshift($rawBreadcrumbsCollection, $rawCrumb);

        // If there's a parent, add it and its parents as well
        if (isset($breadcrumbOptions['parent_route'])) {
            $rawBreadcrumbsCollection = $this->getBreadcrumbs(
                $routeCollection->get($breadcrumbOptions['parent_route']),
                $breadcrumbOptions['parent_route'],
                $routeCollection,
                $rawBreadcrumbsCollection
            );
        }

        return $rawBreadcrumbsCollection;
    }

}
