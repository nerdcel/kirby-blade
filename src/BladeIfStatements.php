<?php

namespace Afbora;

use Illuminate\Support\Facades\Blade;

class BladeIfStatements
{
    public static function register()
    {
        foreach (option('afbora.blade.ifs', []) as $statement => $callback) {
            Blade::if($statement, $callback);
        }
    }
}
