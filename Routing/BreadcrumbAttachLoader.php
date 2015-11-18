<?php

namespace Thormeier\BreadcrumbBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Attaches breadcrumb tree to every routes default config
 */
class BreadcrumbAttachLoader extends DelegatingLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        /** @var RouteCollection $routeCollection */
        $routeCollection = parent::load($resource, $type);

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

        if (!isset($breadcrumbOptions['label'])) {
            throw new \InvalidArgumentException(sprintf(
                'Label missing for route "%s"',
                $routeKey
            ));
        }

        $rawBreadcrumbsCollection[] = array(
            'route' => $routeKey,
            'label' => $breadcrumbOptions['label'],
        );

        return $rawBreadcrumbsCollection;
    }
}
