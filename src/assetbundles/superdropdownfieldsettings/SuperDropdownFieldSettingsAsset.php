<?php
/**
 * Super dropdown plugin for Craft CMS 3.x
 *
 * Adds a field type that generates side-by-side and cascading dropdowns from data.
 *
 * @link      https://github.com/veryfinework
 * @copyright Copyright (c) 2020 veryfinework
 */

namespace veryfinework\superdropdown\assetbundles\superdropdownfieldsettings;

use craft\web\AssetBundle;

/**
 * SuperdropdownFieldAsset AssetBundle
 **
 * @author    veryfinework
 * @package   Superdropdown
 * @since     1.0.0
 */
class SuperdropdownFieldSettingsAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // path to publishable resources
        $this->sourcePath = '@veryfinework/superdropdown/assetbundles/superdropdownfieldsettings/dist';

        $this->css = [
            'css/Superdropdown.css',
        ];

        parent::init();
    }
}
