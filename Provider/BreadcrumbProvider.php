<?php

namespace Thormeier\BreadcrumbBundle\Provider;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Thormeier\BreadcrumbBundle\Model\BreadcrumbCollectionInterface;
use Thormeier\BreadcrumbBundle\Model\BreadcrumbInterface;

/**
 * Breadcrumb factory class that is used to alter breadcrumbs and inject them where needed
 */
class BreadcrumbProvider implements BreadcrumbProviderInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Route name of the current request.
     *
     * @var string
     */
    private $currentRouteName;

    /**
     * @var BreadcrumbCollectionInterface
     */
    private $breadcrumbs = null;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var string
     */
    private $collectionClass;

    /**
     * @param RouterInterface $router
     * @param string          $modelClass
     * @param string          $collectionClass
     */
    public function __construct(RouterInterface $router, $modelClass, $collectionClass)
    {
        $this->router = $router;
        $this->modelClass = $modelClass;
        $this->collectionClass = $collectionClass;
    }

    /**
     * Listen to the kernelRequest event to get the route out of the request
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {
            $this->currentRouteName = $event->getRequest()->get('_route');
        }
    }

    /**
     * @return BreadcrumbCollectionInterface
     */
    public function getBreadcrumbs()
    {
        if (null === $this->breadcrumbs) {
            $parentCollection = $this->router->getRouteCollection();

            // Support for JMS i18n router
            if (method_exists($this->router, 'getOriginalRouteCollection')) {
                $collection = $this->router->getOriginalRouteCollection();
            } else {
                $collection = $parentCollection;
            }

            $this->breadcrumbs = $this->createBreadcrumbsFromRoutes($collection, $parentCollection);
        }

        return $this->breadcrumbs;
    }

    /**
     * Convenience method to get an entry from the breadcrumbs.
     *
     * @param string $route
     *
     * @return BreadcrumbInterface|null
     *
     * @see BreadcrumbCollection::getBreadcrumbByRoute
     */
    public function getBreadcrumbByRoute($route)
    {
        return $this->getBreadcrumbs()->getBreadcrumbByRoute($route);
    }

    /**
     * Creates an array of breadcrumbs from a given RouteCollection
     *
     * @param RouteCollection $routes
     * @param RouteCollection $parentRoutes In the case of JMS i18n router, this collection is different.
     *
     * @return BreadcrumbCollectionInterface
     */
    private function createBreadcrumbsFromRoutes(RouteCollection $routes, RouteCollection $parentRoutes)
    {
        /** @var BreadcrumbCollectionInterface $breadcrumbs */
        $breadcrumbs = new $this->collectionClass();

        $route = $routes->get($this->currentRouteName);
        if (!$route) {
            // we did not find the route of this request. play it safe
            return $breadcrumbs;
        }
        $routeName = $this->currentRouteName;

        if (false === $route->hasOption('breadcrumb')) {
            return $breadcrumbs;
        }

        do {
            $options = $route->getOption('breadcrumb');

            if (null === $options || false === isset($options['label'])) {
                throw new \LogicException(sprintf(
                    'Routes used as parent routes need to be configured as breadcrumbs themselves. Associated route: "%s"',
                    $routeName
                ));
            }

            $breadcrumbs->addBreadcrumbToStart(new $this->modelClass($options['label'], $routeName));

            $parentRouteName = isset($options['parent_route']) ? $options['parent_route'] : null;

            $route = $parentRoutes->get($parentRouteName);
            if (null !== $parentRouteName && !$route) {
                throw new \LogicException(sprintf(
                    'Parent route "%s" specified on route "%s" not found',
                    $parentRouteName,
                    $routeName
                ));
            }
            $routeName = $parentRouteName;
        } while (null !== $parentRouteName);

        return $breadcrumbs;
    }
}
