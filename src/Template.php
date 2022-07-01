<?php

namespace Leitsch\Blade;

use Exception;
use Illuminate\Support\Facades\View;
use Kirby\Cms\App;
use Kirby\Cms\Template as KirbyTemplate;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Tpl;
use Stringable;

class Template extends KirbyTemplate
{
    public const EXTENSION_BLADE = 'blade.php';
    public const EXTENSION_FALLBACK = 'php';

    protected string $templatesPath;
    protected string $viewsPath;
    protected ?string $extension = null;

    public function __construct(App $kirby, string $name, string $type = 'html', string $defaultType = 'html')
    {
        parent::__construct($name, $type, $defaultType);

        $this->templatesPath = Paths::getPathTemplates();
        $this->viewsPath = Paths::getPathViews();
    }

    public function render(array $data = []): string
    {
        if ($this->isBlade()) {
            View::share('kirby', $data['kirby']);
            View::share('site', $data['site']);
            View::share('pages', $data['pages']);
            View::share('page', $data['page']);

            return View::file($this->file(), $data)->render();
        }

        return Tpl::load($this->file(), $data);
    }

    public function isBlade(): bool
    {
        return $this->extension() === static::EXTENSION_BLADE;
    }

    public function extension(): string
    {
        if (! is_null($this->extension)) {
            // return from cache
            return $this->extension;
        }


        $bladeRoot = $this->templatesPath . "/" . $this->name() . "." . static::EXTENSION_BLADE;
        $fallbackRoot = $this->templatesPath . "/" . $this->name() . "." . static::EXTENSION_FALLBACK;
        $bladeExists = file_exists($bladeRoot);

        if ($bladeExists || file_exists($fallbackRoot)) {
            // template from templates folder
            $this->extension = $bladeExists ? static::EXTENSION_BLADE : static::EXTENSION_FALLBACK;
        } else if ($path = App::instance()->extension($this->store(), $this->name())) {
            // template from plugin
            $this->extension = str_ends_with($path, static::EXTENSION_BLADE) ? static::EXTENSION_BLADE : static::EXTENSION_FALLBACK;
        } else {
            // No matching template found, fall back to default extension
            $this->extension = static::EXTENSION_FALLBACK;
        }

        return $this->extension;
    }

    public function file(): ?string
    {
        if ($this->hasDefaultType() === true) {
            // default type template (i.e. not a content representation)

            try {
                // Try the default template in the default template directory.
                return F::realpath($this->getFilename(), $this->templatesPath);
            } catch (Exception $e) {
                // ignore errors, continue searching
            }

            // Look for the default template provided by an extension.
            $path = App::instance()->extension($this->store(), $this->name());

            if ($path !== null) {
                return $path;
            }
        }

        // try to load content represenation instead

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
            return App::instance()->extension($this->store(), $name);
        }
    }

    public function getFilename(?string $name = null): string
    {
        $name = $name ?? $this->name();

        return "{$this->templatesPath}/{$name}.{$this->extension()}";
    }
}
