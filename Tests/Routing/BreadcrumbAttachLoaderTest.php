<?php

namespace Thormeier\BreadcrumbBundle\Tests\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Thormeier\BreadcrumbBundle\Routing\BreadcrumbAttachLoader;

/**
 * Tests the router loader that hooks in and attaches the breadcrumb options to _breadcrumb defaults
 */
class BreadcrumbAttachLoaderTestextends extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BreadcrumbAttachLoader
     */
    private $loader;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Config\Loader\LoaderResolverInterface
     */
    private $delegatingLoader;

    /**
     * Set up mocks for the whole router loader
     */
    public function setUp()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Config\Loader\LoaderInterface $delegatingLoader */
        $delegatingLoader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')
            ->setMethods(array('load'))
            ->getMockForAbstractClass();

        $this->delegatingLoader = $delegatingLoader;
        $this->loader = new BreadcrumbAttachLoader($this->delegatingLoader);
    }

    /**
     * Test the loading and set up of multiple breadcrumbs on mutiple routes
     */
    public function testLoad()
    {
        $collection = new RouteCollection();

        $route1Crumbs = array(
            'breadcrumb' => array(
                'label' => 'Foo',
                'parent_route' => 'bar',
            )
        );
        $route2Crumbs = array(
            'breadcrumb' => array(
                'label' => 'Bar',
            )
        );

        $collection->add('foo', new Route('/foo', array(), array(), $route1Crumbs));
        $collection->add('bar', new Route('/bar', array(), array(), $route2Crumbs));

        $this->delegatingLoader->expects($this->once())
            ->method('load')
            ->will($this->returnValue($collection));

        /** @var RouteCollection $result */
        $result = $this->loader->load('foobar');

        $this->assertCount(2, $result->all());
        $this->assertCount(2, $result->get('foo')->getDefault('_breadcrumbs'));
        $this->assertEquals(array('label' => 'Bar', 'route' => 'bar',), $result->get('foo')->getDefault('_breadcrumbs')[0]);
        $this->assertEquals(array('label' => 'Foo', 'route' => 'foo',), $result->get('foo')->getDefault('_breadcrumbs')[1]);

        $this->assertCount(1, $result->get('bar')->getDefault('_breadcrumbs'));
        $this->assertEquals(array('label' => 'Bar', 'route' => 'bar',), $result->get('bar')->getDefault('_breadcrumbs')[0]);
    }

    /**
     * Test exception if one breadcrumb is missing its label
     */
    public function testMalformedBreadcrumb()
    {
        $collection = new RouteCollection();

        $route1Crumbs = array(
            'breadcrumb' => array(
                // label missing
                'parent_route' => 'bar',
            )
        );
        $route2Crumbs = array(
            'breadcrumb' => array(
                'label' => 'Bar',
            )
        );

        $collection->add('foo', new Route('/foo', array(), array(), $route1Crumbs));
        $collection->add('bar', new Route('/bar', array(), array(), $route2Crumbs));

        $this->delegatingLoader->expects($this->once())
            ->method('load')
            ->will($this->returnValue($collection));

        $this->setExpectedException('\InvalidArgumentException');
        $this->loader->load('foobar');
    }
}
