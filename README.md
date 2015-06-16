BreadcrumbBundle
================

[![Build Status](https://travis-ci.org/thormeier/breadcrumb-bundle.png?branch=master)](https://travis-ci.org/thormeier/breadcrumb-bundle)

## Introduction

This Symfony2 bundle provides easy integration of breadcrumbs in your TWIG templates via route config.
This bundle is heavily inspired by https://github.com/xi-project/xi-bundle-breadcrumbs

## Installation

### Step 1: Composer require

    $ php composer.phar require "thormeier/breadcrumb-bundle":"1.0.*"

### Step2: Enable the bundle in the kernel

    <?php
    // app/AppKernel.php
    
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Thormeier\BreadcrumbBundle\ThormeierBreadcrumbBundle(),
            // ...
        );
    }

## Configuration

In your config.yml:

    # config.yml
    thormeier_breadcrumb: ~

The template defaults to a very basic one, providing a `<ul>` with `<li>` and `<a>` for every breadcrumb.

## Usage

### Basic

A breadcrumb tree is created by the fields `label` and `parent_route` in the `defaults` of a route. Basic tree example:

    # routing.yml
    
    acme_demo_home:
        path: /
        defaults:
            label: Home
    
    acme_demo_contact:
        path: /contact
        defaults:
            label: Contact
            parent_route: acme_demo_home
    
    acme_demo_catalogue:
        path: /catalogue
        defaults:
            label: 'Our Catalogue'
            parent_route: acme_demo_home
    
    acme_demo_catalogue_categories:
        path: /catalogue/categories
        defaults:
            label: 'All categories'
            parent_route: acme_demo_catalogue

Would result in a breadcrumb tree like:

    acme_demo_home
        |- acme_demo_contact
        `- acme_demo_catalogue
           `- acme_demo_catalogue_categories:

If the current route is `acme_demo_catalogue`, the breadcrumbs would for instance show the following:

    Home > Our Catalogue

### Dynamic routes

If you happen to have dynamic routes or dynamic translations that you need in your breadcrumbs, you can set parameters for both directly on the `Breadcrumb` object, for instance:

    <?php
    // MyController
    
    // ...
    
    public function productDetailAction()
    {
        $product = ...;
    
        // ...
    
        $this->get('thormeier.breadcrumb.breadcrumb_provider')
            ->getBreadcrumbByRoute('acme_demo_product_detail')
            ->setRouteParams(array(
                'id' => $product->getId(),
            ))
            ->setLabelParams(array(
                'name' => $product->getName(),
            ));
            
        // ...
    }

### Displaying in twig

Simply call as following:

    {# someTemplate.html.twig #}
    {# ... #}
    
    {{ breadcrumbs() }}
    
    {# ... #}

### Replacing the default template

If you want to use a custom template, add the following to your config.yml

    # config.yml
    thormeier_breadcrumb:
        template: 'my twig template path'

Your custom breadcrumb template receives a variable called `breadcrumbs` that is a collection that represents your breadcrumbs, ordered by first to last.

A single `breadcrumb` has the fields `route`, `routeParams`, `label` and `labelParams`. `route` and `routeParams` are used to generate a path in twig, i.e. `path(breadcrumb.route, breadcrumb.routeParams)`, whereas `label` and `labelParams` are used to generate the text for the breadcrumb, i.e. `{{ (breadcrumb.label)|trans(breadcrumb.labelParams) }}`

Your custom template might look something like this:

    {# myBreadcrumbs.html.twig #}

    <div>
        {% for breadcrumb in breadcrumbs %}
            <a href="{{ path(breadcrumb.route, breadcrumb.routeParams) }}">
                {{ (breadcrumb.label)|trans(breadcrumb.labelParams) }}
            </a>
        {% endfor %}
    </div>

Have a look at `Resources/views/breadcrumbs.html.twig` to see the default implementation
