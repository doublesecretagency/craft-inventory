<?php
/**
 * Inventory plugin for Craft CMS
 *
 * Take stock of your field usage.
 *
 * @author    Double Secret Agency
 * @link      https://www.doublesecretagency.com/
 * @copyright Copyright (c) 2016 Double Secret Agency
 */

namespace doublesecretagency\inventory\web\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Class InventoryAssets
 * @since 2.0.0
 */
class InventoryAssets extends AssetBundle
{

    /** @inheritdoc */
    public function init()
    {
        parent::init();

        $this->sourcePath = '@doublesecretagency/inventory/resources';
        $this->depends = [CpAsset::class];

        $this->css = [
            'css/inventory.css',
        ];

        $this->js = [
            'js/inventory.js',
        ];
    }

}