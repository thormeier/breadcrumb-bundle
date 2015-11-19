<?php

namespace Thormeier\BreadcrumbBundle\Tests\Twig;

use Thormeier\BreadcrumbBundle\Model\BreadcrumbCollection;
use Thormeier\BreadcrumbBundle\Provider\BreadcrumbProvider;
use Thormeier\BreadcrumbBundle\Twig\BreadcrumbExtension;

/**
 * Test for twig extension
 */
class BreadcrumbExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string dummy template name
     */
    private $template = 'foo';

    /**
     * @var array Dummy crumb data
     */
    private $crumbs = array();

    /**
     * @var string Dummy string that functions as rendered template
     */
    private $renderedTemplate = 'bar';

    /**
     * Test rendering call of breadcrumb extension
     */
    public function testRenderBreadcrumbs()
    {
        $twigEnv = $this->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $twigEnv->expects($this->once())
            ->method('render')
            ->will($this->returnCallback(array($this, 'renderCallback')));

        /** @var \PHPUnit_FrameWork_MockObject_MockObject|BreadcrumbProvider $provider */
        $provider = $this->getMockBuilder('\Thormeier\BreadcrumbBundle\Provider\BreadcrumbProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $provider->expects($this->once())
            ->method('getBreadcrumbs')
            ->will($this->returnValue(new BreadcrumbCollection()));

        $extension = new BreadcrumbExtension($provider, $this->template);

        $this->assertEquals($this->renderedTemplate, $extension->renderBreadcrumbs($twigEnv));
    }

    /**
     * Callback of twigEnv->render
     *
     * @param string $template
     * @param array  $templateArgs
     *
     * @return string
     */
    public function renderCallback($template, array $templateArgs)
    {
        $this->assertEquals($this->template, $template);
        $this->assertArrayHasKey('breadcrumbs', $templateArgs);
        $this->assertEquals($this->crumbs, $templateArgs['breadcrumbs']);

        return $this->renderedTemplate;
    }
}
