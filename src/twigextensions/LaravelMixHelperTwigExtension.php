<?php
/**
 * LaravelMixHelper plugin for Craft CMS 3.x
 *
 * A helper to handle laravel mix
 *
 * @link      https://derekcodes.com
 * @copyright Copyright (c) 2021 Derek Pollard
 */

namespace dpollard\laravelmixhelper\twigextensions;

use Craft;
use craft\helpers\FileHelper;
use dpollard\laravelmixhelper\LaravelMixHelper;

// use Craft;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    Derek Pollard
 * @package   LaravelMixHelper
 * @since     1.0.0
 */
class LaravelMixHelperTwigExtension extends AbstractExtension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'LaravelMixHelper';
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('mix', [$this, 'laravel_mix']),
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
    * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('mix', [$this, 'laravel_mix']),
        ];
    }

    /**
     * Our function called via Twig; it can do anything you want
     *
     * @param null $text
     *
     * @return string
     */
    public function laravel_mix($text = null)
    {
        $base = Craft::getAlias('@root');
        $paths = $this->find_laravel_mix_manifest_and_hot();

        $manifestPath = $paths['manifestPath'];
        $hotPath = $paths['hotPath'];

        if (!$manifestPath) {
            return $text;
        }

        $path = '';

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (array_key_exists($text, $manifest)) {
            $path = $manifest[$text];
        }

        if ($hotPath && $path) {
            $url = trim(file_get_contents($hotPath));

            if (str_ends_with($url, '/') && str_starts_with($path, '/')) {
                $url = rtrim($url, "/");
            }

            $path = $url . $path;
        }

        return $path ?: $text;
    }

    private function find_laravel_mix_manifest_and_hot()
    {
        if (! defined('DIRECTORY_SEPARATOR')) {
            define('DIRECTORY_SEPARATOR', '/');
        }

        $checkDirectories = ['', 'web', 'public'];
        $base = Craft::getAlias('@root');

        $mixManifestPath = null;
        $hotPath = null;

        foreach ($checkDirectories as $checkDirectory) {
            $dir = !$checkDirectory ? $base : $base . DIRECTORY_SEPARATOR . $checkDirectory;

            if (!is_dir($dir)) {
                continue;
            }

            $files = FileHelper::findFiles($dir, [
                'filter' => static function ($path) {
                    return str_ends_with($path, 'mix-manifest.json') || str_ends_with($path, 'hot');
                }
            ]);

            if (count($files) < 1) {
                continue;
            }

            foreach ($files as $file) {
                if (str_ends_with($file, 'mix-manifest.json')) {
                    $mixManifestPath = $file;
                }

                if (str_ends_with($file, 'hot')) {
                    $hotPath = $file;
                }
            }

            break;
        }

        return ['manifestPath' => $mixManifestPath, 'hotPath' => $hotPath];
    }
}
