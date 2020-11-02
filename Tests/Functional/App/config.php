<?php

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
$container->loadFromExtension('thormeier_breadcrumb', null);

$container->loadFromExtension('framework', array(
    'secret' => 'foobar',
    'session' => array(
        'storage_id' => 'session.storage.mock_file'
    ),
    'router' => array(
        'resource' => realpath('./Tests/Functional/App/routes.yml'),
    ),
));
