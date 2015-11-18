<?php

namespace Thormeier\BreadcrumbBundle\Tests\Provider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Thormeier\BreadcrumbBundle\Model\Breadcrumb;
use Thormeier\BreadcrumbBundle\Model\BreadcrumbCollection;
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
     * @var \PHPUnit_Framework_MockObject_MockObject|Request
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

        $this->provider = new BreadcrumbProvider(self::MODEL_CLASS, self::COLLECTION_CLASS);
    }

    /**
     * Tests the outcome if there are no configured breadcrumbs
     */
    public function testGetNoConfiguredBreadcrumbs()
    {
        $this->request->expects($this->any())
            ->method('get')
            ->will($this->returnValue(array()));

        $this->provider->onKernelRequest($this->responseEvent);
        $result = $this->provider->getBreadcrumbs();

        $this->assertInstanceOf('\Thormeier\BreadcrumbBundle\Model\BreadcrumbCollection', $result);
        $this->assertEmpty($result->getAll());
    }

    /**
     * Test the generation of a single breadcrumb
     */
    public function testSingleBreadcrumb()
    {
        $label = 'foo';
        $route = 'bar';

        $this->request->expects($this->any())
            ->method('get')
            ->will($this->returnValue(array(
                array(
                    'label' => $label,
                    'route' => $route,
                ),
            )));

        $this->provider->onKernelRequest($this->responseEvent);
        $result = $this->provider->getBreadcrumbs();

        $this->assertCount(1, $result->getAll());

        $this->assertEquals($label, $result->getAll()[0]->getLabel());
        $this->assertEquals($route, $result->getAll()[0]->getRoute());
    }

    /**
     * Test the generation of multiple breadcrumbs
     */
    public function testMultipleBreadcrumbs()
    {
        $label1 = 'foo';
        $route1 = 'bar';
        $label2 = 'baz';
        $route2 = 'qux';

        $this->request->expects($this->any())
            ->method('get')
            ->will($this->returnValue(array(
                array(
                    'label' => $label1,
                    'route' => $route1,
                ),
                array(
                    'label' => $label2,
                    'route' => $route2,
                ),
            )));

        $this->provider->onKernelRequest($this->responseEvent);
        $result = $this->provider->getBreadcrumbs();

        $this->assertCount(2, $result->getAll());

        $this->assertEquals($label1, $result->getAll()[0]->getLabel());
        $this->assertEquals($route1, $result->getAll()[0]->getRoute());

        $this->assertEquals($label2, $result->getAll()[1]->getLabel());
        $this->assertEquals($route2, $result->getAll()[1]->getRoute());
    }
}
