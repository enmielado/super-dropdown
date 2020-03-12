<?php
/**
 * Super dropdown plugin for Craft CMS 3.x
 *
 * Adds a field type that generates side-by-side and cascading dropdowns from data.
 *
 * @link      https://github.com/veryfinework
 * @copyright Copyright (c) 2020 veryfinework
 */

namespace veryfinework\superdropdown;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

use veryfinework\superdropdown\fields\Superdropdown as SuperdropdownField;
use veryfinework\superdropdown\variables\SuperdropdownVariable;

/**
 *
 * @author    veryfinework
 * @package   Superdropdown
 * @since     1.0.0
 *
 */
class SuperDropdown extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Superdropdown::$plugin
     *
     * @var Superdropdown
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register the field
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = SuperdropdownField::class;
            }
        );

        // Register the variable
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('superdropdown', SuperdropdownVariable::class);
            }
        );

        // log plugin
        Craft::info(
            Craft::t(
                'super-dropdown',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

}
