<?php

use Kirby\Cms\App as Kirby;
use Kirby\Cms\App;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;
use Leitsch\Blade\BladeDirectives;
use Leitsch\Blade\BladeFactory;
use Leitsch\Blade\BladeIfStatements;
use Leitsch\Blade\Helpers;
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

            // load components from `site/components` directory (basically works
            // like page models)
            // site/components/demo.php => \DemoComponent
            // site/components/demo/sub.php => \Demo_SubComponent
            // site/components/demo/sub/sub.php => \Demo_Sub_SubComponent
            $path = $this->root('site') . '/components';

            foreach (Dir::index($path, true) as $model) {
                if (pathinfo($model, PATHINFO_EXTENSION) !== 'php') {
                    continue;
                }

                $parts = ltrim(Str::after($model, $path), '/');
                $parts = explode('/', $parts);
                $last = count($parts) - 1;
                $parts[$last] = F::name($parts[$last]);

                $class = implode('_', array_map(fn($item) => str_replace(['.', '-', '_'], '', $item), $parts)) . 'Component';
                $name = implode('.', $parts);

                // load the model class
                F::loadOnce($path . '/' . $model);

                if (class_exists($class)) {
                    // ensure to register a fully-qualifield classname including
                    // namespace, so it works in any namespace
                    $r = new ReflectionClass($class);
                    $componentModels[$r->getNamespaceName() . '\\' . $r->getName()] = $name;
                }
            }

            // get components that have been registred by other plugins
            foreach (App::instance()->plugins() as $plugin) {
                $componentModels = array_merge(
                    $componentModels,
                    array_flip($plugin->extends()['bladeComponents'] ?? []),
                );
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
