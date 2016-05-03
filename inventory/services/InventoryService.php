<?php
/**
 * Inventory plugin for Craft CMS
 *
 * Inventory Service
 *
 * @author    Double Secret Agency
 * @copyright Copyright (c) 2016 Double Secret Agency
 * @link      http://www.doublesecretagency.com/
 * @package   Inventory
 * @since     1.0.0
 */

namespace Craft;

class InventoryService extends BaseApplicationComponent
{

	/**
	 */
	public function getFieldLayouts(&$context)
	{
		$context['fieldLayouts'] = array();

		foreach ($context['fields'] as $field) {

			$layouts = array();
			foreach ($this->_getRelatedLayoutIds($field) as $layoutId) {
				$layouts[] = $this->_getLayoutData($layoutId);
			}

			$context['fieldLayouts'][] = array(
				'field'   => $field,
				'layouts' => $layouts,
			);
		}
	}

	/**
	 */
	private function _getRelatedLayoutIds($field)
	{
		return craft()->db->createCommand()
			->select(craft()->db->tablePrefix.'fieldlayoutfields.layoutId')
			->from('fieldlayoutfields')
			->where('fieldId=:id', array(':id' => $field->id))
			->queryColumn();
	}

	/**
	 */
	private function _getLayoutData($layoutId)
	{

		// Get element type
		$eType = craft()->db->createCommand()
			->select('type')
			->from('fieldlayouts')
			->where('id=:id', array(':id' => $layoutId))
			->queryScalar();
		$elementType = craft()->elements->getElementType($eType);

		// Set default data
		$data = array(
			'id'          => $layoutId,
			'elementType' => $elementType->name,
			'section'     => null,
			'entryType'   => null,
			'editLayout'  => null,
			'tab'         => null,
		);

		// Configure based on element type
		switch ($eType) {

			case 'Entry':

				// Get entry type
				$entryType = craft()->db->createCommand()
					->select('id,name,sectionId')
					->from('entrytypes')
					->where('fieldLayoutId=:id', array(':id' => $layoutId))
					->queryRow();

				// Get section
				$section = craft()->sections->getSectionById($entryType['sectionId']);

				// Get tab
				$tab = craft()->db->createCommand()
					->select('name')
					->from('fieldlayouttabs')
					->where('layoutId=:id', array(':id' => $layoutId))
					->queryScalar();

				// Set entry layout data
				$data['section']    = ($section ? $section->name : null);
				$data['entryType']  = $entryType['name'];
				$data['tab']        = $tab;

				// Edit layout
				$editLayoutPath = 'settings/sections/'.$entryType['sectionId'].'/entrytypes/'.$entryType['id'];
				$data['editLayout'] = UrlHelper::getCpUrl($editLayoutPath);

				break;

			case 'GlobalSet':

				// Get global set
				$globalSet = craft()->db->createCommand()
					->select('id,name')
					->from('globalsets')
					->where('fieldLayoutId=:id', array(':id' => $layoutId))
					->queryRow();

				$data['section'] = ($globalSet ? $globalSet['name'] : null);

				// Edit layout
				$editLayoutPath = 'settings/globals/'.$globalSet['id'].'#set-fieldlayout';
				$data['editLayout'] = UrlHelper::getCpUrl($editLayoutPath);

				break;

			case 'Asset':

				// Get asset source
				$assetSource = craft()->db->createCommand()
					->select('name')
					->from('assetsources')
					->where('fieldLayoutId=:id', array(':id' => $layoutId))
					->queryScalar();

				$data['section'] = ($assetSource ? $assetSource : null);

				break;

			// case 'Category':
			// 	$data['section']   = null;
			// 	$data['entryType'] = null;
			// 	$data['tab']       = null;
			// 	break;

			// case 'Commerce_Product':
			// 	$data['section']   = null;
			// 	$data['entryType'] = null;
			// 	$data['tab']       = null;
			// 	break;

			// case 'Commerce_Variant':
			// 	$data['section']   = null;
			// 	$data['entryType'] = null;
			// 	$data['tab']       = null;
			// 	break;

		}

		// Return layout data
		return $data;
	}

}