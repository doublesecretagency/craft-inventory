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

namespace doublesecretagency\inventory\utilities;

use Craft;
use craft\base\Utility;

/**
 * Class FieldInventoryUtility
 * @since 3.0.0
 */
class FieldInventoryUtility extends Utility
{

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('inventory', 'Field Inventory');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'inventory';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath(): ?string
    {
        // Set the icon mask path
        $iconPath = Craft::getAlias('@vendor/doublesecretagency/craft-inventory/src/icon-mask.svg');

        // If not a string, bail
        if (!is_string($iconPath)) {
            return null;
        }

        // Return the icon mask path
        return $iconPath;
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        // Render the utility template
        return Craft::$app->getView()->renderTemplate('inventory/fields');
    }

}
