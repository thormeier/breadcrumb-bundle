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
     * Builds an array of breadcrumbs for the given route recursively
     *
     * @param Route           $route
     * @param string          $routeKey
     * @param RouteCollection $routeCollection
     *
     * @return array
     */
    private function getBreadcrumbs(Route $route, $routeKey, RouteCollection $routeCollection)
    {
        $breadcrumbOptions = $route->getOption('breadcrumb');

        $rawBreadcrumbsCollection = array();
        if (isset($breadcrumbOptions['parent_route'])) {
            $rawBreadcrumbsCollection = $this->getBreadcrumbs(
                $routeCollection->get($breadcrumbOptions['parent_route']),
                $breadcrumbOptions['parent_route'],
                $routeCollection
            );
        }

        if (false === isset($breadcrumbOptions['label'])) {
            throw new \InvalidArgumentException(sprintf(
                'Label for breadcrumb on route "%s" must be configured',
                $routeKey
            ));
        }

        $rawBreadcrumbsCollection[] = array(
            'route' => $routeKey,
            'label' => $breadcrumbOptions['label'],
        );

        return $rawBreadcrumbsCollection;
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
}
