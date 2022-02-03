<?php

use Afbora\BladeDirectives;
use Afbora\BladeFactory;
use Afbora\BladeIfStatements;
use Afbora\Paths;
use Afbora\Template;
use Kirby\Cms\App as Kirby;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('afbora/blade', [
    'options' => [
        'views' => function () {
            return kirby()->roots()->cache() . '/views';
        },
        'directives' => [],
        'ifs' => [],
        'minify' => [
            'enabled' => false,
            'options' => [],
        ]
    ],
    'components' => [
        'template' => function (Kirby $kirby, string $name, string $contentType = null) {
            return new Template($kirby, $name, $contentType);
        }
    ],
    'hooks' => [
        'system.loadPlugins:after' => function () {
            BladeFactory::register([Paths::getPathTemplates()], Paths::getPathViews());
            BladeDirectives::register();
            BladeIfStatements::register();
        },
    ],
    'routes' => [
        [
            // Block all requests to /url.blade and return 404
            'pattern' => '(:all)\.blade',
            'action' => function ($all) {
                return false;
            }
        ]
    ]
]);
