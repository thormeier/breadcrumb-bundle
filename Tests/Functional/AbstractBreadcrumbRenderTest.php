<?php

namespace Thormeier\BreadcrumbBundle\Tests\Functional;

use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Thormeier\BreadcrumbBundle\Tests\Functional\App\AppKernel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class AbstractBreadcrumbRenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AppKernel Kernel with all necessary services to test the bundle.
     */
    protected $kernel;

    /**
     * Sets up the AppKernel and a console application
     */
    protected function setUp()
    {
        $kernel = new AppKernel('test', false);
        $kernel->boot();

        /** @var Environment $twigEnv */
        $twigEnv = $kernel->getContainer()->get('twig');

        /** @var FilesystemLoader $twigLoader */
        $twigLoader = $twigEnv->getLoader();
        $twigLoader->addPath(realpath('./Tests/Functional/Resources/views'));

        $this->kernel = $kernel;

        /** @var LoaderResolver $loaderResolver */
        $this->kernel->getContainer()->get('routing.loader')->load(realpath('./Tests/Functional/App/routes.yml'));
    }

    /**
     * @param $url
     * @return string
     * @throws \Exception
     */
    public function doRequest($url)
    {
        $request = Request::create($url);

        /** @var Response $response */
        $response = $this->kernel->handle($request);

        return $response->getContent();
    }
}
