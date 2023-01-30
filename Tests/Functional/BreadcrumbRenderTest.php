<?php

namespace Thormeier\BreadcrumbBundle\Tests\Functional;

class BreadcrumbRenderTest extends AbstractBreadcrumbRenderTest
{
    /**
     * @throws \Exception
     */
    public function testSingleBreadcrumb()
    {
        $response = $this->doRequest('/');
        $this->assertContains('<a title="Home">Home</a>', $response);
    }

    /**
     * @throws \Exception
     */
    public function testDoubleBreadcrumb()
    {
        $response = $this->doRequest('/catalogue');
        $this->assertContains('<a href="/" title="Home">Home</a>', $response);
        $this->assertContains('<a title="Our Catalogue">Our Catalogue</a>', $response);
    }
}
