<?php

namespace Afbora;

class Paths
{
    public static function getPathTemplates(): string
    {
        $optionPath = option('afbora.blade.templates');

        if ($optionPath !== null && is_dir($optionPath)) {
            if (is_callable($optionPath)) {
                return $optionPath();
            }

            $path = kirby()->roots()->index() . "/" . $optionPath;
        } else {
            $path = kirby()->root('templates');
        }

        return $path;
    }

    public static function getPathViews(): string
    {
        $path = option('afbora.blade.views');

        if (is_callable($path)) {
            return $path();
        }

        return $path;
    }
}
