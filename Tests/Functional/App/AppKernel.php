<?php

namespace Thormeier\BreadcrumbBundle\Tests\Functional\App;

use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Thormeier\BreadcrumbBundle\ThormeierBreadcrumbBundle;

/**
 * Simple AppKernel for tests
 */
class AppKernel extends Kernel
{
    /**
     * Registers the TwigBundle, the BreadcrumbBundle and the FrameworkBundle
     *
     * @return array
     */
    public function registerBundles()
    {
        return array(
            new TwigBundle(),
            new FrameworkBundle(),
            new ThormeierBreadcrumbBundle(),
        );
    }

    /**
     * Load mocked config
     *
     * @param LoaderInterface $loader
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.php');
    }
}
