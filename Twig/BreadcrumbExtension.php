<?php

namespace Thormeier\BreadcrumbBundle\Twig;

use Thormeier\BreadcrumbBundle\Provider\BreadcrumbProviderInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for breadcrumbs: Render a given template
 */
class BreadcrumbExtension extends AbstractExtension
{
    /**
     * @var BreadcrumbProviderInterface
     */
    private $breadcrumbProvider;

    /**
     * @var string
     */
    private $template;

    /**
     * @param BreadcrumbProviderInterface $breadcrumbProvider
     */
    public function __construct(BreadcrumbProviderInterface $breadcrumbProvider, $template)
    {
        $this->breadcrumbProvider = $breadcrumbProvider;
        $this->template = $template;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'breadcrumbs',
                array(
                    $this,
                    'renderBreadcrumbs'
                ),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * @param Environment $twigEnvironment
     *
     * @return string
     */
    public function renderBreadcrumbs(Environment $twigEnvironment)
    {
        return $twigEnvironment->render($this->template, array(
            'breadcrumbs' => $this->breadcrumbProvider->getBreadcrumbs()->getAll()
        ));
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getName()
    {
        return 'thormeier.breadcrumb_bundle.twig_extension';
    }
}
