<?php

namespace Thormeier\BreadcrumbBundle\Model;

/**
 * Interface for type hinting and having a similar interface for custom implementations
 */
interface BreadcrumbInterface
{
    /**
     * @param string $label
     * @param string $route
     * @param array  $routeParameters
     * @param array  $labelParameters
     */
    public function __construct($label, $route, array $routeParameters = array(), array $labelParameters = array());

    /**
     * @return string
     */
    public function getRoute();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param array $routeParameters
     *
     * @return $this
     */
    public function setRouteParameters(array $routeParameters);

    /**
     * @param array $labelParameters
     *
     * @return $this
     */
    public function setLabelParameters(array $labelParameters);

    /**
     * @return array
     */
    public function getRouteParameters();

    /**
     * @return array
     */
    public function getLabelParameters();
}
