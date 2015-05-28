<?php

namespace Thormeier\BreadcrumbBundle\Tests\Model;

use Thormeier\BreadcrumbBundle\Model\Breadcrumb;
use Thormeier\BreadcrumbBundle\Model\BreadcrumbCollection;

/**
 * BreadcrumbCollectionTest
 *
 * Test array logic of collection
 */
class BreadcrumbCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test normal adding of breadcrumbs
     */
    public function testAddBreadcrumb()
    {
        $breadcrumbA = new Breadcrumb('foo', 'bar');
        $breadcrumbB = new Breadcrumb('bar', 'baz');

        $expected = array($breadcrumbA, $breadcrumbB);

        $collection = new BreadcrumbCollection();
        $collection->addBreadcrumb($breadcrumbA)->addBreadcrumb($breadcrumbB);

        $this->assertEquals($expected, $collection->getAll());
    }

    /**
     * Test adding of breadcrumb before another known one
     */
    public function testAddBeforeCrumb()
    {
        $breadcrumbA = new Breadcrumb('foo', 'bar');
        $breadcrumbB = new Breadcrumb('bar', 'baz');
        $breadcrumbC = new Breadcrumb('baz', 'qux');

        $expected = array($breadcrumbA, $breadcrumbB, $breadcrumbC);

        $collection = new BreadcrumbCollection();
        $collection->addBreadcrumb($breadcrumbA)->addBreadcrumb($breadcrumbC);

        $collection->addBreadcrumbBeforeCrumb($breadcrumbB, $breadcrumbC);

        $this->assertEquals($expected, $collection->getAll());
    }

    /**
     * Test adding of breadcrumb after another known one
     */
    public function testAddAfterCrumb()
    {
        $breadcrumbA = new Breadcrumb('foo', 'bar');
        $breadcrumbB = new Breadcrumb('bar', 'baz');
        $breadcrumbC = new Breadcrumb('baz', 'qux');

        $expected = array($breadcrumbA, $breadcrumbC, $breadcrumbB);

        $collection = new BreadcrumbCollection();
        $collection->addBreadcrumb($breadcrumbA)->addBreadcrumb($breadcrumbC);

        $collection->addBreadcrumbAfterCrumb($breadcrumbB, $breadcrumbC);

        $this->assertEquals($expected, $collection->getAll());
    }

    /**
     * Test adding of breadcrumb to the very start
     */
    public function testAddBreadcrumbToStart()
    {
        $breadcrumbA = new Breadcrumb('foo', 'bar');
        $breadcrumbB = new Breadcrumb('bar', 'baz');
        $breadcrumbC = new Breadcrumb('baz', 'qux');

        $expected = array($breadcrumbC, $breadcrumbA, $breadcrumbB);

        $collection = new BreadcrumbCollection();
        $collection->addBreadcrumb($breadcrumbA)->addBreadcrumb($breadcrumbB);

        $collection->addBreadcrumbToStart($breadcrumbC);

        $this->assertEquals($expected, $collection->getAll());
    }

    /**
     * Test getting a breadcrumb by a known route
     */
    public function testGetBreadcrumbByRoute()
    {
        $breadcrumbA = new Breadcrumb('foo', 'bar');
        $breadcrumbB = new Breadcrumb('bar', 'baz');
        $breadcrumbC = new Breadcrumb('baz', 'qux');

        $collection = new BreadcrumbCollection();
        $collection->addBreadcrumb($breadcrumbA)->addBreadcrumb($breadcrumbB)->addBreadcrumb($breadcrumbC);

        $this->assertEquals($breadcrumbB, $collection->getBreadcrumbByRoute('baz'));
        $this->assertEquals($breadcrumbA, $collection->getBreadcrumbByRoute('bar'));
    }
}
