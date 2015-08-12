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
     * @var string
     */
    private $currentRoute;

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
            $this->currentRoute = $event->getRequest()->get('_route');
        }
    }

    /**
     * @return BreadcrumbCollectionInterface
     */
    public function getBreadcrumbs()
    {
        if (null === $this->breadcrumbs) {
            // Support for JMS i18n router
            if (method_exists($this->router, 'getOriginalRouteCollection')) {
                $collection = $this->router->getOriginalRouteCollection();
            } else {
                $collection = $this->router->getRouteCollection();
            }

            $this->breadcrumbs = $this->createBreadcrumbsFromRoutes($collection);
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
     *
     * @return BreadcrumbCollectionInterface
     */
    private function createBreadcrumbsFromRoutes(RouteCollection $routes)
    {
        /** @var BreadcrumbCollectionInterface $breadcrumbs */
        $breadcrumbs = new $this->collectionClass();

        $currentRoute = $routes->get($this->currentRoute);
        $currentRouteName = $this->currentRoute;

        if (false === $currentRoute->hasOption('breadcrumb')) {
            return $breadcrumbs;
        }

        do {
            $options = $currentRoute->getOption('breadcrumb');

            if (null === $options || false === isset($options['label'])) {
                throw new \LogicException(sprintf(
                    'Routes used as parent routes need to be configured as breadcrumbs themselves. Associated route: "%s"',
                    $currentRouteName
                ));
            }

            $breadcrumbs->addBreadcrumbToStart(new $this->modelClass($options['label'], $currentRouteName));

            $parentRoute = isset($options['parent_route']) ? $options['parent_route'] : null;

            $currentRoute = $routes->get($parentRoute);
            $currentRouteName = $parentRoute;
        } while (null !== $parentRoute);

        return $breadcrumbs;
    }
}
