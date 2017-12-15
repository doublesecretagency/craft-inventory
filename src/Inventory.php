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

namespace doublesecretagency\inventory;

use yii\base\Event;

use Craft;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use doublesecretagency\inventory\services\InventoryService;

/**
 * Class Inventory
 * @since 2.0.0
 */
class Inventory extends Plugin
{

    /** @var Plugin  $plugin  Self-referential plugin property. */
    public static $plugin;

    /** @var bool  $hasCpSection  The plugin has its own section. */
    public $hasCpSection = true;

    /** @inheritDoc */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Load plugin components
        $this->setComponents([
            'inventoryService' => InventoryService::class,
        ]);

        // Load class services
        $inventoryService = Inventory::$plugin->inventoryService;

        // Set template hook
        Craft::$app->getView()->hook('getFieldLayouts', [$inventoryService, 'getFieldLayouts']);

        // Register CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['inventory/fields/<groupId:\d+>'] = ['template' => 'inventory/fields'];
            }
        );
    }

    /** @inheritDoc */
    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['subnav'] = [
            'fields' => ['label' => 'Fields', 'url' => 'inventory/fields'],
            // TODO: 'assets' => ['label' => 'Assets', 'url' => 'inventory/assets'],
        ];
        return $item;
    }

}