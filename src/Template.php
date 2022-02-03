<?php

namespace Leitsch\Blade;

use Exception;
use Illuminate\Support\Facades\View;
use Kirby\Cms\App as Kirby;
use Kirby\Cms\Template as KirbyTemplate;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Tpl;
use voku\helper\HtmlMin;

class Template extends KirbyTemplate
{
    protected string $templatesPath;
    protected string $viewsPath;

    public function __construct(Kirby $kirby, string $name, string $type = 'html', string $defaultType = 'html')
    {
        parent::__construct($name, $type, $defaultType);

        $this->templatesPath = Paths::getPathTemplates();
        $this->viewsPath = Paths::getPathViews();
    }

    public function render(array $data = []): string
    {
        if ($this->isBlade() && $this->hasDefaultType() === true) {
            View::share('kirby', $data['kirby']);
            View::share('site', $data['site']);
            View::share('pages', $data['pages']);
            View::share('page', $data['page']);

            $html = View::make($this->name, $data)->render();
        } else {
            $html = Tpl::load($this->file(), $data);
        }

        if (option('leitsch.blade.minify.enabled', false) === true) {
            $htmlMin = new HtmlMin();
            $options = option('leitsch.blade.minify.options', []);

            foreach ($options as $option => $status) {
                if (method_exists($htmlMin, $option)) {
                    $htmlMin->{$option}((bool)$status);
                }
            }

            return $htmlMin->minify($html);
        }

        return $html;
    }

    public function isBlade(): bool
    {
        return file_exists($this->templatesPath . "/" . $this->name() . "." . $this->bladeExtension());
    }

    public function bladeExtension(): string
    {
        return 'blade.php';
    }

    public function file(): ?string
    {
        if ($this->hasDefaultType() === true) {
            try {
                // Try the default template in the default template directory.
                return F::realpath($this->getFilename(), $this->templatesPath);
            } catch (Exception $e) {
                //
            }

            // Look for the default template provided by an extension.
            $path = Kirby::instance()->extension($this->store(), $this->name());

            if ($path !== null) {
                return $path;
            }
        }

        // disallow blade extension for content representation, for ex: /blog.blade
        if ($this->type() === 'blade') {
            return null;
        } else {
            $name = $this->name() . "." . $this->type();
        }

        try {
            // Try the template with type extension in the default template directory.
            return F::realpath($this->getFilename($name), $this->templatesPath);
        } catch (Exception $e) {
            // Look for the template with type extension provided by an extension.
            // This might be null if the template does not exist.
            return Kirby::instance()->extension($this->store(), $name);
        }
    }

    public function getFilename(string $name = null): string
    {
        if ($name) {
            return $this->templatesPath . "/" . $name . "." . $this->extension();
        }

        if ($this->isBlade()) {
            return $this->templatesPath . "/" . $this->name() . "." . $this->bladeExtension();
        }

        return $this->templatesPath . "/" . $this->name() . "." . $this->extension();
    }
}
