<?php

namespace Thormeier\BreadcrumbBundle\Tests\Provider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Thormeier\BreadcrumbBundle\Model\Breadcrumb;
use Thormeier\BreadcrumbBundle\Provider\BreadcrumbProvider;

/**
 * Provider class test
 */
class BreadcrumbProviderTest extends \PHPUnit_Framework_TestCase
{
    const MODEL_CLASS = 'Thormeier\BreadcrumbBundle\Model\Breadcrumb';

    const COLLECTION_CLASS = 'Thormeier\BreadcrumbBundle\Model\BreadcrumbCollection';

    /**
     * @var GetResponseEvent
     */
    private $responseEvent;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var BreadcrumbProvider
     */
    private $provider;

    /**
     * Set up the whole
     */
    public function setUp()
    {
        $this->request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->responseEvent = $this->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getRequestType', 'getRequest'))
            ->getMock();
        $this->responseEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->responseEvent->expects($this->any())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST));

        /** @var RouterInterface $router */
        $router = $this->getMockedRouter();

        $this->provider = new BreadcrumbProvider($router, self::MODEL_CLASS, self::COLLECTION_CLASS);
    }

    /**
     * Test getting of all breadcrumbs
     *
     * @param string $route
     * @param array  $expectedTree
     *
     * @dataProvider getBreadcrumbsProvider
     */
    public function testGetBreadcrumbs($route, array $expectedTree)
    {
        $this->request->expects($this->any())
            ->method('get')
            ->will($this->returnValue($route));

        $this->provider->onKernelRequest($this->responseEvent);

        $this->assertEquals($expectedTree, $this->provider->getBreadcrumbs()->getAll());
    }

    /**
     * Data provider method for testGetBreadcrumbs
     *
     * @return array
     */
    public function getBreadcrumbsProvider()
    {
        $breadcrumbA = new Breadcrumb('a', 'a');
        $breadcrumbB = new Breadcrumb('b', 'b');
        $breadcrumbC = new Breadcrumb('c', 'c');
        $breadcrumbD = new Breadcrumb('d', 'd');

        return array(
            array('a', array($breadcrumbA)),
            array('b', array($breadcrumbA, $breadcrumbB)),
            array('c', array($breadcrumbA, $breadcrumbC)),
            array('d', array($breadcrumbD)),
        );
    }

    /**
     * @param string     $currentRoute
     * @param string     $desiredBreadcrumbRoute
     * @param Breadcrumb $expectedResult
     *
     * @dataProvider breadcrumbsByRouteProvider
     */
    public function testGetBreadcrumbByRoute($currentRoute, $desiredBreadcrumbRoute, Breadcrumb $expectedResult = null)
    {
        $this->request->expects($this->any())
            ->method('get')
            ->will($this->returnValue($currentRoute));

        $this->provider->onKernelRequest($this->responseEvent);

        $this->assertEquals($expectedResult, $this->provider->getBreadcrumbByRoute($desiredBreadcrumbRoute));
    }

    /**
     * Data provider method for testGetBreadcrumbByRoute
     *
     * @return array
     */
    public function breadcrumbsByRouteProvider()
    {
        $breadcrumbA = new Breadcrumb('a', 'a');
        $breadcrumbB = new Breadcrumb('b', 'b');
        $breadcrumbC = new Breadcrumb('c', 'c');

        return array(
            array('a', 'a', $breadcrumbA),
            array('a', 'b', null),
            array('b', 'b', $breadcrumbB),
            array('b', 'a', $breadcrumbA),
            array('c', 'a', $breadcrumbA),
            array('c', 'b', null),
            array('c', 'c', $breadcrumbC),
        );
    }

    /**
     * Returns a mocked router service
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockedRouter()
    {
        $routeA = new Route('a', array(), array(), array('breadcrumb' => array(
            'label' => 'a',
        )));
        $routeB = new Route('b', array(), array(), array('breadcrumb' => array(
            'label' => 'b',
            'parent_route' => 'a',
        )));
        $routeC = new Route('c', array(), array(), array('breadcrumb' => array(
            'label' => 'c',
            'parent_route' => 'a',
        )));
        $routeD = new Route('d', array(), array(), array('breadcrumb' => array(
            'label' => 'd',
        )));

        $routeCollection = new RouteCollection();
        $routeCollection->add('a', $routeA);
        $routeCollection->add('b', $routeB);
        $routeCollection->add('c', $routeC);
        $routeCollection->add('d', $routeD);

        $router = $this->getMockBuilder('\Symfony\Component\Routing\RouterInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('addExpressionLanguageProvider', 'getRouteCollection', 'getOriginalRouteCollection'))
            ->getMockForAbstractClass();
        $router->expects($this->any())
            ->method('getRouteCollection')
            ->will($this->returnValue($routeCollection));
        $router->expects($this->any())
            ->method('getOriginalRouteCollection')
            ->will($this->returnValue($routeCollection));

        return $router;
    }
}
