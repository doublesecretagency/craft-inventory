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

use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use doublesecretagency\inventory\services\InventoryService;
use yii\base\Event;

/**
 * Class Inventory
 * @since 2.0.0
 */
class Inventory extends Plugin
{

    /**
     * @var Plugin Self-referential plugin property.
     */
    public static Plugin $plugin;

    /**
     * @var bool The plugin has its own section.
     */
    public bool $hasCpSection = true;

    /**
     * @inheritdoc
     */
    public function init(): void
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

    /**
     * @inheritdoc
     */
    public function getCpNavItem(): ?array
    {
        $item = parent::getCpNavItem();
        $item['subnav'] = [
            'fields' => ['label' => 'Fields', 'url' => 'inventory/fields'],
            // TODO: 'assets' => ['label' => 'Assets', 'url' => 'inventory/assets'],
        ];
        return $item;
    }

}
