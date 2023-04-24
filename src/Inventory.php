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
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\services\Utilities;
use craft\web\UrlManager;
use doublesecretagency\inventory\services\InventoryService;
use doublesecretagency\inventory\utilities\FieldInventoryUtility;
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

        // Whether a cookie has been set to hide "Inventory" from the sidebar
        $hideFromSidebar = Craft::$app->getRequest()->getCookies()->getValue('hide-inventory-from-sidebar');

        // If cookie exists
        if ($hideFromSidebar) {
            // Hide "Inventory" from the sidebar
            $this->hasCpSection = false;
        }

        // Load plugin components
        $this->setComponents([
            'inventoryService' => InventoryService::class,
        ]);

        // Load class services
        $inventoryService = Inventory::$plugin->inventoryService;

        // Set template hook
        Craft::$app->getView()->hook('getFieldLayouts', [$inventoryService, 'getFieldLayouts']);

        // Register
        $this->_registerUtilities();
        $this->_registerCpRoutes();

        // Redirect after plugin install
        $this->_postInstallRedirect();
    }

    // ========================================================================= //

    /**
     * Register utilities.
     */
    private function _registerUtilities(): void
    {
        // If not a web request, bail
        if (!Craft::$app->getRequest()->getIsCpRequest()) {
            return;
        }

        // Load utilities
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            static function(RegisterComponentTypesEvent $event) {
                $event->types[] = FieldInventoryUtility::class;
            }
        );
    }

    /**
     * Register CP routes.
     */
    private function _registerCpRoutes(): void
    {
        // If not a web request, bail
        if (!Craft::$app->getRequest()->getIsCpRequest()) {
            return;
        }

        // Register CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['utilities/inventory/<groupId:\d+>'] = ['template' => 'inventory/fields'];
            }
        );
    }
    // ========================================================================= //

    /**
     * After the plugin has been installed,
     * redirect to the Field Inventory utility.
     */
    private function _postInstallRedirect(): void
    {
        // After the plugin has been installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            static function (PluginEvent $event) {

                // If installed via console, no need for a redirect
                if (Craft::$app->getRequest()->getIsConsoleRequest()) {
                    return;
                }

                // If installed plugin isn't Inventory, bail
                if ('inventory' !== $event->plugin->handle) {
                    return;
                }

                // Redirect to the Field Inventory utility (with a welcome message)
                $url = UrlHelper::cpUrl('utilities/inventory', ['welcome' => 1]);
                Craft::$app->getResponse()->redirect($url)->send();
            }
        );
    }

}
