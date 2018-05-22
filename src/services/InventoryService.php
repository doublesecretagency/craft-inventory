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

namespace doublesecretagency\inventory\services;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\helpers\UrlHelper;

/**
 * Class InventoryService
 * @since 2.0.0
 */
class InventoryService extends Component
{

    /**
     * Collect all layouts based on existing fields.
     *
     * @param array  &$context  The current template context.
     *
     * @return void
     */
    public function getFieldLayouts(&$context)
    {
        $context['fieldLayouts'] = array();

        foreach ($context['fields'] as $field) {

            $layouts = array();
            foreach ($this->_getRelatedLayoutIds($field) as $row) {
                $layouts[] = $this->_getLayoutData($row);
            }

            $context['fieldLayouts'][] = array(
                'field'   => $field,
                'layouts' => $layouts,
            );
        }
    }

    /**
     * Get partial data for all layouts which contain the specified field.
     *
     * @param craft\base\Field  $field  Model of specified field.
     *
     * @return array  Partial data for all layouts which contain the specified field.
     */
    private function _getRelatedLayoutIds($field)
    {
        return (new Query())
            ->select(['layoutId','tabId'])
            ->from(['{{%fieldlayoutfields}}'])
            ->where('fieldId=:id', array(':id' => $field->id))
            ->orderBy('layoutId ASC')
            ->all();
    }

    /**
     * Get relevant layout data based on field's element type.
     *
     * @param array  $row  Partial field layout data.
     *
     * @return array  Relevant layout data to display.
     */
    private function _getLayoutData($row)
    {

        // Get element type
        $eType = (new Query())
            ->select(['type'])
            ->from('{{%fieldlayouts}}')
            ->where('id=:id', array(':id' => $row['layoutId']))
            ->scalar();

        // Set default data
        $data = array(
            'id'          => $row['layoutId'],
            'elementType' => null,
            'section'     => null,
            'entryType'   => null,
            'tab'         => null,
            'editLayout'  => null,
        );

        // If not a valid element type, bail
        if (!class_exists($eType)) {
            $data['elementType'] = '<span class="error">'.$eType.'</span>';
            return $data;
        }

        // Set element type
        $data['elementType'] = $eType::displayName();

        // Configure based on element type
        switch ($eType::refHandle()) {

            case 'entry':

                // Get entry type
                $entryType = (new Query())
                    ->select(['id','name','sectionId'])
                    ->from('{{%entrytypes}}')
                    ->where('fieldLayoutId=:id', array(':id' => $row['layoutId']))
                    ->one();

                // Get section
                $section = Craft::$app->getSections()->getSectionById($entryType['sectionId']);

                // Get tab
                $data['tab'] = (new Query())
                    ->select(['name'])
                    ->from('{{%fieldlayouttabs}}')
                    ->where('id=:id', array(':id' => $row['tabId']))
                    ->scalar();

                // Set entry layout data
                $data['section']    = ($section ? $section->name : null);
                $data['entryType']  = $entryType['name'];

                // Edit layout
                $editLayoutPath = 'settings/sections/'.$entryType['sectionId'].'/entrytypes/'.$entryType['id'];
                $data['editLayout'] = UrlHelper::cpUrl($editLayoutPath);

                break;

            case 'globalset':

                // Get global set
                $globalSet = (new Query())
                    ->select(['id','name'])
                    ->from('{{%globalsets}}')
                    ->where('fieldLayoutId=:id', array(':id' => $row['layoutId']))
                    ->one();

                // Get section
                $data['section'] = ($globalSet ? $globalSet['name'] : null);

                // Get tab
                $data['tab'] = (new Query())
                    ->select(['name'])
                    ->from('{{%fieldlayouttabs}}')
                    ->where('id=:id', array(':id' => $row['tabId']))
                    ->scalar();

                // Edit layout
                $editLayoutPath = 'settings/globals/'.$globalSet['id'].'#set-fieldlayout';
                $data['editLayout'] = UrlHelper::cpUrl($editLayoutPath);

                break;

            case 'asset':

                // Get asset source
                $assetSource = (new Query())
                    ->select(['id','name'])
                    ->from('{{%volumes}}')
                    ->where('fieldLayoutId=:id', array(':id' => $row['layoutId']))
                    ->one();

                // Get section
                $data['section'] = ($assetSource ? $assetSource['name'] : null);

                // Edit layout
                $editLayoutPath = 'settings/assets/volumes/'.$assetSource['id'].'#assetvolume-fieldlayout';
                $data['editLayout'] = UrlHelper::cpUrl($editLayoutPath);

                break;

            case 'user':

                // Get tab
                $data['tab'] = (new Query())
                    ->select(['name'])
                    ->from('{{%fieldlayouttabs}}')
                    ->where('id=:id', array(':id' => $row['tabId']))
                    ->scalar();

                // Edit layout
                $data['editLayout'] = UrlHelper::cpUrl('settings/users/fields');

                break;

            // TODO: Activate other element types

            // case 'category':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

            // case 'tag':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

            // case 'matrixblock':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

            // case 'commerceproduct':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

            // case 'commercevariant':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

        }

        // Return layout data
        return $data;
    }

}