<?php

namespace Thormeier\BreadcrumbBundle\Twig;

use Thormeier\BreadcrumbBundle\Provider\BreadcrumbProvider;

/**
 * Twig extension for breadcrumbs: Render a given template
 */
class BreadcrumbExtension extends \Twig_Extension
{
    /**
     * @var BreadcrumbProvider
     */
    private $breadcrumbProvider;

    /**
     * @var string
     */
    private $template;

    /**
     * @param BreadcrumbProvider $breadcrumbProvider
     */
    public function __construct(BreadcrumbProvider $breadcrumbProvider, $template)
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
            'breadcrumbs' => new \Twig_Function_Method(
                $this,
                'renderBreadcrumbs',
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * @param \Twig_Environment $twigEnvironment
     *
     * @return string
     */
    public function renderBreadcrumbs(\Twig_Environment $twigEnvironment)
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