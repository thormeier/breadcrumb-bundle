MobileDetectLightBundle
=======================

[![Build Status](https://travis-ci.org/thormeier/MobileDetectLightBundle.png?branch=master)](https://travis-ci.org/thormeier/MobileDetectLightBundle)

## Introduction

This Symfony2 bundle provides three twig functions to check if the client is on a mobile or tablet device. This bundle makes use of the class provided by http://mobiledetect.net/.
This bundle is built to be as lightweight as possible to provide a possibility to alter Twig templates according to the clients device.

## Installation

### Step 1: Composer require

    $ php composer.phar require "thormeier/mobile-detect-light-bundle":"1.0.*"

### Step2: Enable the bundle in the kernel

    <?php
    // app/AppKernel.php
    
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Thormeier\MobileDetectLightBundle\ThormeierMobileDetectLightBundle(),
            // ...
        );
    }

## Usage

There are three new Twig functions provided by this bundle:

### is_mobile()

    // template.html.twig

    {% if is_mobile() %}
        {# do something that is only visible for mobile users, i.e. display an app store button or similar #}
    {% endif %}

### is_tablet()

    // template.html.twig

    {% if is_tablet() %}
        {# do something for tablet users only #}
    {% endif %}

### is_desktop()

    // template.html.twig

    {% if is_desktop() %}
        {# do something for desktop users only #}
    {% endif %}