<?php

use Illuminate\Support\Facades\View;
use Kirby\Cms\App as Kirby;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Tpl as Snippet;
use Leitsch\Blade\BladeDirectives;
use Leitsch\Blade\BladeFactory;
use Leitsch\Blade\BladeIfStatements;
use Leitsch\Blade\Paths;
use Leitsch\Blade\Template;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('leitsch/blade', [
    'options' => [
        'views' => function () {
            return kirby()->roots()->cache() . '/views';
        },
        'directives' => [],
        'ifs' => [],
    ],
    'components' => [
        'template' => function (Kirby $kirby, string $name, string $contentType = null) {
            return new Template($kirby, $name, $contentType);
        },
        'snippet' => function (Kirby $kirby, $name, array $data = []): ?string {
            $snippets = A::wrap($name);

            foreach ($snippets as $name) {
                $name = (string)$name;
                $file = null;
                $bladeFile = $kirby->root('snippets') . '/' . $name . '.' . Template::EXTENSION_BLADE;
                $fallbackFile = $kirby->root('snippets') . '/' . $name . '.' . Template::EXTENSION_FALLBACK;

                if (file_exists($bladeFile)) {
                    // blade snippet exists
                    $file = $bladeFile;
                } elseif (file_exists($fallbackFile)) {
                    // vanilla PHP snippets exists
                    $file = $fallbackFile;
                } else {
                    // look for snippet from plugin
                    $file = $kirby->extensions('snippets')[$name] ?? null;
                }

                if ($file) {
                    break;
                }
            }

            if (str_ends_with($file, Template::EXTENSION_BLADE)) {
                // blade snippet
                return View::file($file, $data)->render();
            } else {
                // vanilly PHP snippet
                return Snippet::load($file, $data);
            }
        },
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
            },
        ],
    ],
]);
