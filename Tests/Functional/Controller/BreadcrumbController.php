<?php

namespace Thormeier\BreadcrumbBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BreadcrumbController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function breadcrumbAction()
    {
        return $this->render('breadcrumbs.twig');
    }
}
