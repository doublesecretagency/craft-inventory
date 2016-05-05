<?php
/**
 * Inventory plugin for Craft CMS
 *
 * @author    Double Secret Agency
 * @copyright Copyright (c) 2016 Double Secret Agency
 * @link      http://www.doublesecretagency.com/
 * @package   Inventory
 * @since     1.0.0
 */

namespace Craft;

class InventoryPlugin extends BasePlugin
{

	public function init()
	{
		parent::init();
		$this->_ezHook('getFieldLayouts', craft()->inventory);
	}

	public function getName()
	{
		 return Craft::t('Inventory');
	}

	public function getDescription()
	{
		return Craft::t('Take stock of your field usage.');
	}

	public function getDocumentationUrl()
	{
		return 'https://github.com/lindseydiloreto/craft-inventory';
	}

	public function getVersion()
	{
		return '1.0.1';
	}

	public function getSchemaVersion()
	{
		return '0.0.0';
	}

	public function getDeveloper()
	{
		return 'Double Secret Agency';
	}

	public function getDeveloperUrl()
	{
		return 'https://github.com/lindseydiloreto/craft-inventory';
		// return 'http://www.doublesecretagency.com/';
	}

	public function hasCpSection()
	{
		return true;
	}

	public function registerCpRoutes()
	{
		return array(
			'inventory/fields/(?P<groupId>\d+)' => 'inventory/fields',
		);
	}

	// Easily link hooks to specific service methods
	private function _ezHook($hookName, $service)
	{
		craft()->templates->hook($hookName, array($service, $hookName));
	}

}