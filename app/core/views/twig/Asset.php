<?php
/**
 * Twig Asset
 *
 * @version v0.0.1 (May 23, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\core\views\twig;

/**
 * Twig Extension for dealing with assets.
 */
class Asset extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            'image' => new \Twig_Function_Method($this, 'image')
        ];
    }

    /**
     * Resolves a path to the image subdirectory in the assets directory.
     * @param  string $path The path to resolve.
     * @return string
     */
    public function image($path)
    {
        return "/images/{$path}";
    }
}
