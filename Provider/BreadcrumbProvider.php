<?php

namespace Thormeier\BreadcrumbBundle\Provider;

use Symfony\Bundle\FrameworkBundle\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Thormeier\BreadcrumbBundle\Model\Breadcrumb;
use Thormeier\BreadcrumbBundle\Model\BreadcrumbCollection;

/**
 * Breadcrumb factory class that is used to alter breadcrumbs and inject them where needed
 */
class BreadcrumbProvider
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $currentRoute;

    /**
     * @var BreadcrumbCollection
     */
    private $breadcrumbs = null;

    /**
     * @param Router $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
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
     * @return BreadcrumbCollection
     */
    public function getBreadcrumbs()
    {
        if (null === $this->breadcrumbs) {
            $collection = $this->router->getRouteCollection();

            // Support for JMS i18n router
            if (method_exists($this->router, 'getOriginalRouteCollection')) {
                $collection = $this->router->getOriginalRouteCollection();
            }

            $this->breadcrumbs = $this->createBreadcrumbsFromRoutes($collection);
        }

        return $this->breadcrumbs;
    }

    /**
     * @param string $route
     *
     * @return Breadcrumb|null
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
     * @return BreadcrumbCollection
     */
    private function createBreadcrumbsFromRoutes(RouteCollection $routes)
    {
        $breadcrumbs = new BreadcrumbCollection();

        /** @var Route $lastRoute */
        $currentRoute = $routes->get($this->currentRoute);
        $currentRouteName = $this->currentRoute;

        do {
            $breadcrumbs->addBreadcrumbToStart(new Breadcrumb($currentRoute->getDefault('label'), $currentRouteName));

            $parentRoute = $currentRoute->getDefault('parent_route');

            $currentRoute = $routes->get($parentRoute);
            $currentRouteName = $parentRoute;
        } while (null !== $parentRoute);

        return $breadcrumbs;
    }
}
