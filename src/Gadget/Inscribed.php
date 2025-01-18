<?php

namespace Mdkieran\Inscribed\Gadget;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

abstract class Inscribed
{
    /**
     * The key that the data will reside under (within JavaScript).
     */
    public string $key = 'app';

    /**
     * The location of the output file (within the public path).
     */
    public string $tail = 'build/inscribed/app.js';

    /**
     * The data being passed to JavaScript.
     */
    abstract public function data() : array;

    /**
     * The data compiled into JavaScript.
     */
    public function js() : string
    {
        return Str::replaceArray(
            '?', [$this->key, Js::from($this->data())],
            "window.Inscribed||={};window.Inscribed['?']=?;",
        );
    }

    /**
     * The data compiled into inline JavaScript.
     */
    public function inline() : string
    {
        return '<script>'.$this->js().'</script>';
    }

    /**
     * Save the compiled JavaScript to the output file.
     */
    public function save() : self
    {
        $dir = File::dirname($this->pathname());

        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($this->pathname(), $this->js());

        return $this;
    }

    /**
     * Drop the compiled JavaScript file.
     */
    public function drop() : self
    {
        File::delete($this->pathname());

        return $this;
    }

    /**
     * The pathname is the full path, filename, and extension.
     */
    public function pathname() : string
    {
        return public_path($this->tail);
    }

    /**
     * The tail with a cache busting query string.
     */
    public function tail() : string
    {
        return $this->tail.'?v='.time();
    }
}
