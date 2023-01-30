<?php

namespace Thormeier\BreadcrumbBundle\Provider;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Thormeier\BreadcrumbBundle\Model\Breadcrumb;
use Thormeier\BreadcrumbBundle\Model\BreadcrumbCollectionInterface;
use Thormeier\BreadcrumbBundle\Model\BreadcrumbInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Breadcrumb factory class that is used to generate and alter breadcrumbs and inject them where needed
 */
class BreadcrumbProvider implements BreadcrumbProviderInterface
{
    /**
     * @var array
     */
    private $requestBreadcrumbConfig;

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
     * @param string $modelClass
     * @param string $collectionClass
     */
    public function __construct($modelClass, $collectionClass)
    {
        $this->modelClass = $modelClass;
        $this->collectionClass = $collectionClass;
    }

    /**
     * Listen to the kernelRequest event to get the breadcrumb config from the request
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {

        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {

            $route = $event->getRequest()->attributes->get('_route');
            $routeParams = $event->getRequest()->attributes->get('_route_params', array());
            $breadCrumbs =  $event->getRequest()->attributes->get('_breadcrumbs', array());

            //if key exists, then replace label
            foreach($breadCrumbs as $key => $breadCrumb){

                if($breadCrumb['route'] == $route) {

                    if (array_key_exists($breadCrumb['label'], $routeParams)) {

                        $breadCrumbs[$key]['label'] = $routeParams[$breadCrumb['label']];
                    }
                }
            }

            $this->requestBreadcrumbConfig = $breadCrumbs;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBreadcrumbs()
    {
        if (null === $this->breadcrumbs) {
            $this->breadcrumbs = $this->generateBreadcrumbCollectionFromRequest();
        }

        return $this->breadcrumbs;
    }

    /**
     * Convenience method to get an entry from the breadcrumb list of the current requests route.
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
     * Generates an instance of an implementation of BreadcrumbCollectionInterface,
     * based on the breadcrumb information given by the SF Request
     *
     * @return BreadcrumbCollectionInterface
     */
    private function generateBreadcrumbCollectionFromRequest()
    {
        /** @var BreadcrumbCollectionInterface $collection */
        $collection = new $this->collectionClass();

        $model = $this->modelClass;

        if (null !== $this->requestBreadcrumbConfig) {
            foreach ($this->requestBreadcrumbConfig as $rawCrumb) {

                $collection->addBreadcrumb(new $model(
                    $rawCrumb['label'], $rawCrumb['route']
                ));
            }
        }

        return $collection;
    }
}
