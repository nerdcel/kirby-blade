<?php

namespace Afbora;

use Illuminate\Support\Facades\Blade;

class BladeDirectives
{
    public static function register() {
        Blade::directive('asset', function (string $path) {
            return "<?php echo asset($path) ?>";
        });

        Blade::directive('csrf', function () {
            return "<?php echo csrf() ?>";
        });

        Blade::directive('css', function (string $url, array $options = []) {
            if ($options) {
                return "<?php echo css($url, $options) ?>";
            }

            return "<?php echo css($url) ?>";
        });

        Blade::directive('e', function (mixed $condition, mixed $value, mixed $alternative = null) {
            if ($alternative) {
                return "<?php echo e($condition, $value, $alternative) ?>";
            }

            return "<?php echo e($condition, $value) ?>";
        });

        Blade::directive('get', function (string $key, mixed $default = null) {
            if ($default) {
                return "<?php echo get($key, $default) ?>";
            }

            return "<?php echo get($key) ?>";
        });

        Blade::directive('gist', function (string $url, string $file = null) {
            if ($file) {
                return "<?php echo gist($url, $file) ?>";
            }

            return "<?php echo gist($url) ?>";
        });

        Blade::directive('h', function (string $string, bool $keepTags = false) {
            if ($keepTags) {
                return "<?php echo h($string, $keepTags) ?>";
            }

            return "<?php echo h($string) ?>";
        });

        Blade::directive('html', function (string $string, bool $keepTags = false) {
            if ($keepTags) {
                return "<?php echo html($string, $keepTags) ?>";
            }

            return "<?php echo html($string) ?>";
        });

        Blade::directive('js', function ($url, array $options = []) {
            if ($options) {
                return "<?php echo js($url, $options) ?>";
            }

            return "<?php echo js($url) ?>";
        });

        Blade::directive('image', function (string $path) {
            return "<?php echo image($path) ?>";
        });

        Blade::directive('kirbytag', function (mixed $type, string $value, array $attr = []) {
            if ($attr) {
                return "<?php echo kirbytag($type, $value, $attr) ?>";
            }

            return "<?php echo kirbytag($type, $value) ?>";
        });

        Blade::directive('kirbytext', function (string $text, array $data = []) {
            if ($data) {
                return "<?php echo kirbytext($text, $data) ?>";
            }

            return "<?php echo kirbytext($text) ?>";
        });

        Blade::directive('kirbytextinline', function (string $text, array $data = []) {
            if ($data) {
                return "<?php echo kirbytextinline($text, $data) ?>";
            }

            return "<?php echo kirbytextinline($text) ?>";
        });

        Blade::directive('kt', function (string $text, array $data = []) {
            if ($data) {
                return "<?php echo kirbytext($text, $data) ?>";
            }

            return "<?php echo kirbytext($text) ?>";
        });

        Blade::directive('markdown', function (string $text) {
            return "<?php echo markdown($text) ?>";
        });

        Blade::directive('option', function (string $key, mixed $default = null) {
            if ($default) {
                return "<?php echo option($key, $default) ?>";
            }

            return "<?php echo option($key) ?>";
        });


        Blade::directive('param', function (string $key, string $fallback = null) {
            if ($fallback) {
                return "<?php echo param($key, $fallback) ?>";
            }

            return "<?php echo param($key) ?>";
        });

        Blade::directive('size', function (mixed $value) {
            return "<?php echo size($value) ?>";
        });

        Blade::directive('smartypants', function (string $text) {
            return "<?php echo smartypants($text) ?>";
        });

        Blade::directive('snippet', function (string $name, mixed $data = null) {
            if ($data) {
                return "<?php echo snippet($name, $data) ?>";
            }

            return "<?php echo snippet($name) ?>";
        });

        Blade::directive('svg', function (string $file) {
            return "<?php echo svg($file) ?>";
        });

        Blade::directive('t', function (mixed $key, string $fallback = null) {
            if ($fallback) {
                return "<?php echo t($key, $fallback) ?>";
            }

            return "<?php echo t($key) ?>";
        });

        Blade::directive('tc', function (mixed $key, int $count) {
            return "<?php echo tc($key, $count) ?>";
        });

        Blade::directive('twitter', function (string $username, string $text = null, string $title = null, string $class = null) {
            if ($text) {
                return "<?php echo twitter($username, $text) ?>";
            } elseif ($text && $title) {
                return "<?php echo twitter($username, $text, $title) ?>";
            } elseif ($text && $title && $class) {
                return "<?php echo twitter($username, $text, $title, $class) ?>";
            }

            return "<?php echo twitter($username) ?>";
        });

        Blade::directive('u', function (string $path = null, mixed $options = null) {
            if ($options) {
                return "<?php echo u($path, $options) ?>";
            }

            return "<?php echo u($path) ?>";
        });

        Blade::directive('url', function (string $path = null, mixed $options = null) {
            if ($path) {
                return "<?php echo url($path) ?>";
            } elseif ($path && $options) {
                return "<?php echo url($path, $options) ?>";
            }

            return "<?php echo url() ?>";
        });

        Blade::directive('video', function (string $url, array $options = [], array $attr = []) {
            if ($options) {
                return "<?php echo video($url, $options) ?>";
            } elseif ($options && $attr) {
                return "<?php echo video($url, $options, $attr) ?>";
            }

            return "<?php echo video($url) ?>";
        });

        Blade::directive('vimeo', function (string $url, array $options = [], array $attr = []) {
            if ($options) {
                return "<?php echo vimeo($url, $options) ?>";
            } elseif ($options && $attr) {
                return "<?php echo vimeo($url, $options, $attr) ?>";
            }

            return "<?php echo vimeo($url) ?>";
        });

        Blade::directive('widont', function (string $string) {
            return "<?php echo widont($string) ?>";
        });

        Blade::directive('youtube', function (string $url, array $options = [], array $attr = []) {
            if ($options) {
                return "<?php echo youtube($url, $options) ?>";
            } elseif ($options && $attr) {
                return "<?php echo youtube($url, $options, $attr) ?>";
            }

            return "<?php echo youtube($url) ?>";
        });

        foreach ($directives = option('afbora.blade.directives', []) as $directive => $callback) {
            Blade::directive($directive, $callback);
        }
    }
}
