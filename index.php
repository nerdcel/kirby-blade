<?php

use Kirby\Cms\App as Kirby;
use Kirby\Filesystem\F;
use Leitsch\Blade\BladeDirectives;
use Leitsch\Blade\BladeFactory;
use Leitsch\Blade\BladeIfStatements;
use Leitsch\Blade\Paths;
use Leitsch\Blade\Snippet;
use Leitsch\Blade\Template;

@include_once __DIR__ . '/vendor/autoload.php';


/**
 * Don’t use class_alias here, because that would break autocomplete
 * in most IDEs.
 */
abstract class BladeComponent extends Illuminate\View\Component
{

}

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
            return (new Snippet($kirby, $name, $data))->load();
        },
    ],
    'hooks' => [
        'system.loadPlugins:after' => function () {

            $componentModels = [];

            foreach (glob($this->root('site') . '/components/*.php') as $model) {
                $name  = F::name($model);
                $class = str_replace(['.', '-', '_'], '', $name) . 'Component';
    
                // load the model class
                F::loadOnce($model);

                if (class_exists($class) === true) {
                    $r = new ReflectionClass($class);
                    $componentModels[$r->getNamespaceName() . '\\' . $r->getName()] = $name;
                }
            }
            
            BladeFactory::register(
                [Paths::getPathTemplates()],
                Paths::getPathViews(),
                $componentModels
            );
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
