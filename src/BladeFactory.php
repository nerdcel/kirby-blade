<?php

namespace Afbora;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\View;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class BladeFactory
{
    protected Factory $viewFactory;

    public function __construct(array $pathsToTemplates, string $pathToCompiledTemplates)
    {
        $container = App::getInstance();

        // we have to bind our app class to the interface
        // as the blade compiler needs the `getNamespace()` method to guess Blade component FQCNs
        $container->instance(Application::class, $container);

        // Dependencies
        $filesystem = new Filesystem;
        $eventDispatcher = new Dispatcher($container);

        // Create View Factory capable of rendering PHP and Blade templates
        $viewResolver = new EngineResolver;
        $bladeCompiler = new BladeCompiler($filesystem, $pathToCompiledTemplates);

        $viewResolver->register('blade', fn () => new CompilerEngine($bladeCompiler));

        $viewFinder = new FileViewFinder($filesystem, $pathsToTemplates);
        $this->viewFactory = new Factory($viewResolver, $viewFinder, $eventDispatcher);
        $this->viewFactory->setContainer($container);
        Facade::setFacadeApplication($container);
        $container->instance(\Illuminate\Contracts\View\Factory::class, $this->viewFactory);
        $container->alias(
            \Illuminate\Contracts\View\Factory::class,
            (new class extends View {
                public static function getFacadeAccessor()
                {
                    return parent::getFacadeAccessor();
                }
            })::getFacadeAccessor()
        );
        $container->instance(BladeCompiler::class, $bladeCompiler);
        $container->alias(
            BladeCompiler::class,
            (new class extends \Illuminate\Support\Facades\Blade {
                public static function getFacadeAccessor()
                {
                    return parent::getFacadeAccessor();
                }
            })::getFacadeAccessor()
        );
    }

    public function getViewFactory(): Factory
    {
        return $this->viewFactory;
    }
}
