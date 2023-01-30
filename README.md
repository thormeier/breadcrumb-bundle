BreadcrumbBundle
================

## Introduction

This is fork of [https://github.com/thormeier/breadcrumb-bundle](https://github.com/thormeier/breadcrumb-bundle) with merged changes and fixes for symfony 5.x.
This Symfony bundle provides integration of breadcrumbs via route config and rendering in your Twig templates.
This bundle is heavily inspired by the inactive [https://github.com/xi-project/xi-bundle-breadcrumbs](https://github.com/xi-project/xi-bundle-breadcrumbs)

## Installation / Getting started

### Step 1: Composer require

    $ php composer.phar require "enoptea/breadcrumb-bundle"

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

Enable the bundle in your config.yml:

    # config.yml
    thormeier_breadcrumb: ~

The template defaults to a basic one, providing a `<ul>` with `<li>` and `<a>` for every breadcrumb.

## Usage

### Basic

A breadcrumb tree is created by the fields `label` and `parent_route` in the `defaults` of a route. Basic tree example:

    # routing.yml
    
    acme_demo_home:
        path: /
        options:
            breadcrumb:
                label: Home
    
    acme_demo_contact:
        path: /contact
        options:
            breadcrumb:
                label: Contact
                parent_route: acme_demo_home
    
    acme_demo_catalogue:
        path: /catalogue
        options:
            breadcrumb:
                label: 'Our Catalogue'
                parent_route: acme_demo_home
    
    acme_demo_catalogue_categories:
        path: /catalogue/categories
        options:
            breadcrumb:
                label: 'All categories'
                parent_route: acme_demo_catalogue

Would result in a breadcrumb tree like:

    acme_demo_home
        |- acme_demo_contact
        `- acme_demo_catalogue
           `- acme_demo_catalogue_categories

If the current route is `acme_demo_catalogue`, the breadcrumbs would for instance show the following:

    Home > Our Catalogue

Since the configuration of the breadcrumbs happens on routing config, it's generally agnostic from _how_ the routing 
configuration happens. This means that configuring breadcrumbs for instance via annotations is perfectly possible:

    /**
     * ...
     * @Route(
     *    "/contact",
     *    name="acme_demo_contact",
     *    options={
     *        "breadcrumb" = {
     *            "label" = "Contact",
     *            "parent_route" = "acme_demo_home"
     *        }
     *    })
     * ...
     */

The configuration can also be done in XML and PHP.

### Dynamic routes

If you happen to have dynamic routes or dynamic translations that you need in your breadcrumbs, they 
can be defined like so:

    # routing.yml
    
    acme_demo_product_detail:
        path: /products/{id}
        options:
            breadcrumb:
                label: 'Produkt: %%name%%'
                parent_route: acme_demo_catalogue

(This example uses a string with a placeholder in the routing directly. You can also define the label text in a 
translation file and only use the translation key as the label. The template will handle the translation and 
replacing.)

**Notice the double `%` to escape the parameter in the label. This needs to be done, because `routing.yml` 
is being parsed by the Symfony container and recognizes constructs, such as `%name%` as a container parameter 
and tries to inject those. The double-`%` escapes it, the template is handling the rest.**

You can then set parameters for both directly on the `Breadcrumb` object, for instance:

    <?php
    // MyController
    
    // ...
    
    public function productDetailAction()
    {
        $product = ...;
    
        // ...
    
        $this->get('thormeier_breadcrumb.breadcrumb_provider')
            ->getBreadcrumbByRoute('acme_demo_product_detail')
            ->setRouteParams(array(
                'id' => $product->getId(),
            ))
            ->setLabelParams(array(
                'name' => $product->getName(),
            ));
            
        // ...
    }

Please note that the breadcrumb must be defined on the route in order to set parameters.

### Dynamic breadcrumbs

If you happen to have a dynamic routing tree, for instance a tree of category pages that can go infinitely deep, 
you can add breadcrumbs that are not defined on a route on the fly. For instance like this:

    <?php
    
    use Thormeier\BreadcrumbBundle\Model\Breadcrumb;
    
    // ...
    
    // Route of the product, we want the categories before this
    $productCrumb = $breadcrumbProvder->getBreadcrumbByRoute('acme_demo_product_detail');
    $collection = $breadcrumbProvider->getBreadcrumbs();
    
    foreach ($product->getCategories() as $category) {
        $newCrumb = new Breadcrumb(
            'Category: %name%',              // Label
            'acme_demo_category',            // Route
            ['id' => $category->getId()],    // Route params
            ['name' => $category->getName()] // Label params
        );
        
        // Adds $newCrumb right in front of $productCrumb
        $collection->addBreadcrumbBeforeCrumb($newCrumb, $productCrumb);
        
        // Or: ->addBreadcrumb(), ->addBreadcrumbAtPosition(), ->addBreadcrumbAfterCrumb(), ->addBreadcrumbToStart()
    }

These breadcrumbs are not stored in the cache though.

### Displaying in twig

Call the twig extension as following:

    {# someTemplate.html.twig #}
    {# ... #}
    
    {{ breadcrumbs() }}
    
    {# ... #}

### Using the bootstrap template

The bundle also provides a default implementation for [Bootstrap](https://getbootstrap.com/docs/4.3/components/breadcrumb/). It can be used as follows:

    # config.yml
    thormeier_breadcrumb:
        template: @ThormeierBreadcrumb/breadcrumbs_bootstrap.html.twig

### Replacing the default template

If you want to use a custom template, add the following to your config.yml

    # config.yml
    thormeier_breadcrumb:
        template: 'my twig template path'

Your custom breadcrumb template receives a variable called `breadcrumbs` that is a collection that represents your 
breadcrumbs, ordered by highest in the tree to lowest.

A single `breadcrumb` has the fields `route`, `routeParameters`, `label` and `labelParameters`. `route` and `routeParameters` 
are used to generate a path in twig, i.e. `path(breadcrumb.route, breadcrumb.routeParameters)`, whereas `label` and 
`labelParameters` are used to generate the text for the breadcrumb, i.e. 
`{{ (breadcrumb.label)|trans(breadcrumb.labelParameters) }}`

Your custom template might look something like this:

    {# myBreadcrumbs.html.twig #}

    <div>
        {% for breadcrumb in breadcrumbs %}
            <a href="{{ path(breadcrumb.route, breadcrumb.routeParameters) }}">
                {{ breadcrumb.label|replace({"%%": "%"})|trans(breadcrumb.labelParameters) }}
            </a>
        {% endfor %}
    </div>

**The replacing of `%%` with single `%` happens inside the template. See *"Dynamic routes"* as why this is needed.**

Have a look at `Resources/views/breadcrumbs.html.twig` and `Resources/views/breadcrumbs_bootstrap.html.twig` to see the default implementations.

### Customize implementations

The model class and/or its collection can be replaced by own implementations, that implement the 
`Thormeier\BreadcrumbBundle\Model\BreadcrumbInterface` and 
`Thormeier\BreadcrumbBundle\Model\BreadcrumbCollectionInterface`:

    # config.yml
    thormeier_breadcrumb:
        model_class: Acme\Breadcrumbs\Model\MyModel
        collection_class: Acme\Breadcrumbs\Model\MyCollection

The provider service ID can be replaced by setting the parameter `provider_service_id`

    # config.yml
    thormeier_breadcrumb:
        provider_service_id: acme.breadcrumbs.my_provider

### Caching

This bundle uses the routing cache to store breadcrumb lists per route on `cache:warmup`. They are then turned into 
a `BreadcrumbCollection` on demand.

## Slides

A slideshow presenting the bundle and explaining some concepts a little further is available on slideshare: 
[http://www.slideshare.net/Thormeier/thormeierbreadcrumbbundle](http://www.slideshare.net/Thormeier/thormeierbreadcrumbbundle)

## Development

To execute tests locally, execute the following command:

```
./vendor/bin/phpunit
```

The folder `coverage/` is ignored by git, it can therefore be used for generating coverage reports with `phpunit --coverage-html ./coverage/`.

If you want to contribute to this project, please feel free to open up issues and/or pull requests!
