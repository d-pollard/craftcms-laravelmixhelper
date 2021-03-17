<?php
/**
 * LaravelMixHelper plugin for Craft CMS 3.x
 *
 * A helper to handle laravel mix
 *
 * @link      https://derekcodes.com
 * @copyright Copyright (c) 2021 Derek Pollard
 */

namespace dpollard\laravelmixhelpertests\unit;

use Codeception\Test\Unit;
use UnitTester;
use Craft;
use dpollard\laravelmixhelper\LaravelMixHelper;

/**
 * ExampleUnitTest
 *
 *
 * @author    Derek Pollard
 * @package   LaravelMixHelper
 * @since     1.0.0
 */
class ExampleUnitTest extends Unit
{
    // Properties
    // =========================================================================

    /**
     * @var UnitTester
     */
    protected $tester;

    // Public methods
    // =========================================================================

    // Tests
    // =========================================================================

    /**
     *
     */
    public function testPluginInstance()
    {
        $this->assertInstanceOf(
            LaravelMixHelper::class,
            LaravelMixHelper::$plugin
        );
    }

    /**
     *
     */
    public function testCraftEdition()
    {
        Craft::$app->setEdition(Craft::Pro);

        $this->assertSame(
            Craft::Pro,
            Craft::$app->getEdition()
        );
    }
}
